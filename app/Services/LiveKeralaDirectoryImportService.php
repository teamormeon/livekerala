<?php

namespace App\Services;

use App\Models\CountryCities;
use App\Models\Listing;
use App\Models\ListingCategoryDetails;
use App\Models\PurchasePackage;
use App\Models\User;
use App\Traits\Upload;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LiveKeralaDirectoryImportService
{
    use Upload;

    /**
     * Import public LiveKerala directory listings into the local directory.
     *
     * @return array<string, mixed>
     */
    public function import(User $owner, PurchasePackage $purchasePackage, array $options = []): array
    {
        $sourceUrl = rtrim($options['source_url'] ?? 'https://www.livekerala.com', '/');
        $maxPages = max(1, (int) ($options['max_pages'] ?? 250));
        $limit = max(0, (int) ($options['limit'] ?? 0));
        $dryRun = (bool) ($options['dry_run'] ?? false);

        $listingUrls = $this->discoverListingUrls($sourceUrl, $maxPages);
        if ($limit > 0) {
            $listingUrls = array_slice($listingUrls, 0, $limit);
        }

        $maps = $this->buildMaps();
        $results = [
            'discovered' => count($listingUrls),
            'imported' => 0,
            'skipped' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($listingUrls as $url) {
            try {
                $payload = $this->extractListingPayload($url, $maps);

                if (($payload['skip_reason'] ?? null) !== null) {
                    $results['skipped']++;
                    $results['errors'][] = $url . ' => ' . $payload['skip_reason'];
                    continue;
                }

                if ($dryRun) {
                    $results['imported']++;
                    continue;
                }

                DB::beginTransaction();

                $existingListing = Listing::where('slug', $payload['source_slug'])->first();
                if ($existingListing) {
                    DB::rollBack();
                    $results['skipped']++;
                    $results['errors'][] = $url . ' => listing already imported';
                    continue;
                }

                $listing = new Listing();
                $listing->user_id = $owner->id;
                $listing->purchase_package_id = $purchasePackage->id;
                $listing->title = $payload['title'];
                $listing->slug = $payload['slug'];
                $listing->category_id = $payload['category_ids'];
                $listing->email = $payload['email'];
                $listing->phone = $payload['phone'];
                $listing->description = $payload['description'];
                $listing->country_id = $payload['country_id'];
                $listing->state_id = $payload['state_id'];
                $listing->city_id = $payload['city_id'];
                $listing->address = $payload['address'];
                $listing->lat = $payload['lat'];
                $listing->long = $payload['long'];
                $listing->status = 0;
                $listing->is_active = 1;
                $listing->save();

                if (!empty($payload['thumbnail_url'])) {
                    try {
                        $thumbnail = $this->fileUpload(
                            $payload['thumbnail_url'],
                            config('filelocation.listing_thumbnail.path'),
                            null,
                            null,
                            'webp',
                            99
                        );

                        if ($thumbnail) {
                            $listing->thumbnail = $thumbnail['path'];
                            $listing->thumbnail_driver = $thumbnail['driver'];
                            $listing->save();
                        }
                    } catch (\Throwable $exception) {
                        Log::warning('LiveKerala import thumbnail upload failed', [
                            'url' => $url,
                            'message' => $exception->getMessage(),
                        ]);
                    }
                }

                DB::commit();
                $results['imported']++;
            } catch (\Throwable $exception) {
                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }
                $results['failed']++;
                $results['errors'][] = $url . ' => ' . $exception->getMessage();
            }
        }

        return $results;
    }

    /**
     * @return array<int, string>
     */
    protected function discoverListingUrls(string $sourceUrl, int $maxPages): array
    {
        $queue = [$this->normalizeUrl($sourceUrl)];
        $visited = [];
        $listings = [];

        while (!empty($queue) && count($visited) < $maxPages) {
            $currentUrl = array_shift($queue);
            if (!$currentUrl || isset($visited[$currentUrl])) {
                continue;
            }

            $visited[$currentUrl] = true;
            $html = $this->fetchHtml($currentUrl);
            if (!$html) {
                continue;
            }

            foreach ($this->extractLinks($html, $currentUrl) as $link) {
                if ($this->isListingUrl($link)) {
                    $listings[$link] = true;
                    continue;
                }

                if ($this->shouldQueueUrl($link, $sourceUrl) && !isset($visited[$link])) {
                    $queue[] = $link;
                }
            }
        }

        ksort($listings);

        return array_keys($listings);
    }

    /**
     * @return array<string, mixed>
     */
    protected function extractListingPayload(string $url, array $maps): array
    {
        $html = $this->fetchHtml($url);
        if (!$html) {
            return ['skip_reason' => 'unable to fetch listing page'];
        }

        $text = $this->normalizeWhitespace(strip_tags($html));
        $title = $this->extractMetaContent($html, 'property', 'og:title')
            ?? $this->extractMetaContent($html, 'name', 'title')
            ?? $this->extractTagContent($html, 'title');

        $title = trim((string) preg_replace('/\s*[-|]\s*Live\s+Kerala.*$/i', '', (string) $title));
        if ($title === '') {
            return ['skip_reason' => 'title not found'];
        }

        $description = $this->extractSection($text, 'description', ['contact', 'enquiry', 'search for']);
        $address = $this->extractLabeledValue($text, 'Address', ['Mobile', 'E-mail', 'Email', 'Website', 'Category', 'Location']);
        $phone = $this->sanitizePhone($this->extractLabeledValue($text, 'Mobile', ['E-mail', 'Email', 'Website', 'Category', 'Location']));
        $email = $this->extractEmail($this->extractLabeledValue($text, 'E-mail', ['Website', 'Category', 'Location'])
            ?: $this->extractLabeledValue($text, 'Email', ['Website', 'Category', 'Location'])
            ?: $text);
        $categoryText = $this->extractLabeledValue($text, 'Category', ['Location', 'Image', 'Featured Image', 'Enquiry', 'Search For']);
        $location = $this->extractLabeledValue($text, 'Location', ['Image', 'Featured Image', 'Enquiry', 'Search For']);
        $thumbnailUrl = $this->extractMetaContent($html, 'property', 'og:image');

        $categoryIds = $this->resolveCategoryIds($categoryText, $maps['category_map']);
        if (empty($categoryIds)) {
            return ['skip_reason' => 'no matching category found for "' . ($categoryText ?: 'unknown') . '"'];
        }

        $locationMatch = $this->resolveLocation($location ?: $address, $address, $maps['cities']);
        if ($locationMatch === null) {
            return ['skip_reason' => 'no matching Kerala city found'];
        }

        $description = $description !== '' ? $description : ($address ?: $location ?: $title);
        $sourceSlug = $this->makeSourceSlug($url, $title);
        $slug = $this->makeUniqueSlug($sourceSlug);

        return [
            'title' => $title,
            'source_slug' => $sourceSlug,
            'slug' => $slug,
            'category_ids' => $categoryIds,
            'email' => $email,
            'phone' => $phone,
            'description' => $description,
            'country_id' => $locationMatch['country_id'],
            'state_id' => $locationMatch['state_id'],
            'city_id' => $locationMatch['city_id'],
            'address' => $address ?: ($location ?: $title),
            'lat' => $locationMatch['lat'],
            'long' => $locationMatch['long'],
            'thumbnail_url' => $thumbnailUrl,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildMaps(): array
    {
        $categoryMap = [];
        $categories = ListingCategoryDetails::query()->select('listing_category_id', 'name')->get();
        foreach ($categories as $category) {
            $normalized = $this->normalizeKey($category->name);
            if ($normalized !== '' && !isset($categoryMap[$normalized])) {
                $categoryMap[$normalized] = (int) $category->listing_category_id;
            }
        }

        $cities = CountryCities::query()
            ->where('status', 1)
            ->select('id', 'name', 'state_id', 'country_id', 'latitude', 'longitude')
            ->get()
            ->map(function ($city) {
                return [
                    'city_id' => (int) $city->id,
                    'state_id' => (int) $city->state_id,
                    'country_id' => (int) $city->country_id,
                    'name' => (string) $city->name,
                    'normalized_name' => $this->normalizeKey($city->name),
                    'lat' => $city->latitude,
                    'long' => $city->longitude,
                ];
            })
            ->filter(fn ($city) => $city['normalized_name'] !== '')
            ->sortByDesc(fn ($city) => strlen($city['normalized_name']))
            ->values()
            ->all();

        return [
            'category_map' => $categoryMap,
            'cities' => $cities,
        ];
    }

    protected function fetchHtml(string $url): ?string
    {
        try {
            $response = Http::timeout(20)
                ->retry(2, 500)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; LiveKeralaImporter/1.0)',
                ])
                ->get($url);

            if (!$response->successful()) {
                Log::warning('LiveKerala import request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                ]);
                return null;
            }

            return $response->body();
        } catch (\Throwable $exception) {
            Log::warning('LiveKerala import request exception', [
                'url' => $url,
                'message' => $exception->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * @return array<int, string>
     */
    protected function extractLinks(string $html, string $baseUrl): array
    {
        preg_match_all('/href\s*=\s*["\']([^"\']+)["\']/i', $html, $matches);
        $links = [];

        foreach ($matches[1] ?? [] as $href) {
            $normalized = $this->normalizeUrl($href, $baseUrl);
            if ($normalized !== null) {
                $links[$normalized] = true;
            }
        }

        return array_keys($links);
    }

    protected function isListingUrl(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        return preg_match('#^/dir/[^/]+/?$#i', $path) === 1;
    }

    protected function shouldQueueUrl(string $url, string $sourceUrl): bool
    {
        $normalized = $this->normalizeUrl($url);
        if ($normalized === null || $this->isListingUrl($normalized)) {
            return false;
        }

        $sourceHost = parse_url($sourceUrl, PHP_URL_HOST);
        $host = parse_url($normalized, PHP_URL_HOST);
        if (!$sourceHost || !$host || !str_ends_with($host, preg_replace('/^www\./', '', $sourceHost))) {
            return false;
        }

        $path = strtolower(parse_url($normalized, PHP_URL_PATH) ?: '/');
        $blockedStarts = [
            '/blog',
            '/quick',
            '/contact',
            '/login',
            '/register',
            '/pricing',
            '/about',
            '/privacy',
            '/terms',
            '/event',
            '/events',
            '/faq',
            '/storage',
            '/assets',
            '/admin',
            '/user',
            '/password',
        ];

        foreach ($blockedStarts as $blocked) {
            if ($path === $blocked || str_starts_with($path, $blocked . '/')) {
                return false;
            }
        }

        if (preg_match('/\.(jpg|jpeg|png|gif|webp|svg|pdf|zip|css|js)$/i', $path)) {
            return false;
        }

        return true;
    }

    protected function normalizeUrl(?string $url, ?string $baseUrl = null): ?string
    {
        $url = trim((string) $url);
        if ($url === '' || str_starts_with($url, '#') || str_starts_with(strtolower($url), 'javascript:') || str_starts_with(strtolower($url), 'mailto:') || str_starts_with(strtolower($url), 'tel:')) {
            return null;
        }

        if ($baseUrl && !preg_match('#^https?://#i', $url)) {
            if (str_starts_with($url, '//')) {
                $scheme = parse_url($baseUrl, PHP_URL_SCHEME) ?: 'https';
                $url = $scheme . ':' . $url;
            } else {
                $baseParts = parse_url($baseUrl);
                if (!$baseParts || empty($baseParts['scheme']) || empty($baseParts['host'])) {
                    return null;
                }

                if (str_starts_with($url, '/')) {
                    $url = $baseParts['scheme'] . '://' . $baseParts['host'] . $url;
                } else {
                    $basePath = $baseParts['path'] ?? '/';
                    $baseDir = rtrim(str_replace('\\', '/', dirname($basePath)), '/');
                    $baseDir = $baseDir === '' ? '' : $baseDir;
                    $url = $baseParts['scheme'] . '://' . $baseParts['host'] . $baseDir . '/' . $url;
                }
            }
        }

        if (!preg_match('#^https?://#i', $url)) {
            return null;
        }

        $parts = parse_url($url);
        if (!$parts || empty($parts['host'])) {
            return null;
        }

        $scheme = strtolower($parts['scheme'] ?? 'https');
        $host = strtolower($parts['host']);
        $path = $parts['path'] ?? '/';
        $path = preg_replace('#/+#', '/', $path);
        $path = $path !== '/' ? rtrim($path, '/') : $path;
        $query = '';

        if (!empty($parts['query'])) {
            parse_str($parts['query'], $queryData);
            ksort($queryData);
            if (!empty($queryData)) {
                $query = '?' . http_build_query($queryData);
            }
        }

        return $scheme . '://' . $host . $path . $query;
    }

    protected function extractMetaContent(string $html, string $attribute, string $value): ?string
    {
        $pattern = '/<meta[^>]*' . preg_quote($attribute, '/') . '=["\']' . preg_quote($value, '/') . '["\'][^>]*content=["\']([^"\']+)["\'][^>]*>/i';
        if (preg_match($pattern, $html, $matches) === 1) {
            return html_entity_decode(trim($matches[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $reversePattern = '/<meta[^>]*content=["\']([^"\']+)["\'][^>]*' . preg_quote($attribute, '/') . '=["\']' . preg_quote($value, '/') . '["\'][^>]*>/i';
        if (preg_match($reversePattern, $html, $matches) === 1) {
            return html_entity_decode(trim($matches[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return null;
    }

    protected function extractTagContent(string $html, string $tag): ?string
    {
        if (preg_match('/<' . preg_quote($tag, '/') . '[^>]*>(.*?)<\/' . preg_quote($tag, '/') . '>/is', $html, $matches) === 1) {
            return $this->normalizeWhitespace(strip_tags($matches[1]));
        }

        return null;
    }

    protected function extractSection(string $text, string $startLabel, array $endLabels): string
    {
        $endPattern = implode('|', array_map(fn ($label) => preg_quote($label, '/'), $endLabels));
        $pattern = '/\b' . preg_quote($startLabel, '/') . '\b\s*(.*?)\s*(?=\b(?:' . $endPattern . ')\b|$)/is';

        if (preg_match($pattern, $text, $matches) === 1) {
            return trim($matches[1]);
        }

        return '';
    }

    protected function extractLabeledValue(string $text, string $label, array $nextLabels): string
    {
        $nextPattern = implode('|', array_map(fn ($nextLabel) => preg_quote($nextLabel, '/'), $nextLabels));
        $pattern = '/\b' . preg_quote($label, '/') . '\b\s*(.*?)\s*(?=\b(?:' . $nextPattern . ')\b|$)/is';

        if (preg_match($pattern, $text, $matches) === 1) {
            return trim($matches[1]);
        }

        return '';
    }

    protected function extractEmail(string $value): ?string
    {
        if (preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $value, $matches) === 1) {
            return strtolower($matches[0]);
        }

        return null;
    }

    protected function sanitizePhone(string $value): ?string
    {
        $value = preg_replace('/[^0-9+]+/', '', $value);
        return $value !== '' ? $value : null;
    }

    /**
     * @return array<int, int>
     */
    protected function resolveCategoryIds(string $categoryText, array $categoryMap): array
    {
        $aliases = [
            'tv chanels' => 'tv channels',
            'hospital' => 'hospitals',
            'youtubers' => 'youtubers',
        ];

        $categories = preg_split('/\s*,\s*/', $categoryText, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $categoryIds = [];

        foreach ($categories as $category) {
            $normalized = $this->normalizeKey($category);
            if ($normalized === '') {
                continue;
            }

            $normalized = $aliases[$normalized] ?? $normalized;
            $candidates = [
                $normalized,
                $this->normalizeKey(Str::plural($normalized)),
                $this->normalizeKey(Str::singular($normalized)),
            ];

            foreach ($candidates as $candidate) {
                if ($candidate !== '' && isset($categoryMap[$candidate])) {
                    $categoryIds[] = $categoryMap[$candidate];
                    break;
                }
            }
        }

        return array_values(array_unique($categoryIds));
    }

    protected function resolveLocation(?string $location, ?string $address, array $cities): ?array
    {
        $haystack = $this->normalizeKey(trim(($location ?? '') . ' ' . ($address ?? '')));
        if ($haystack === '') {
            return null;
        }

        foreach ($cities as $city) {
            $needle = $city['normalized_name'];
            if ($needle !== '' && str_contains(' ' . $haystack . ' ', ' ' . $needle . ' ')) {
                return $city;
            }
        }

        return null;
    }

    protected function makeSourceSlug(string $url, string $title): string
    {
        $slug = trim((string) basename(parse_url($url, PHP_URL_PATH) ?: ''), '/');
        $slug = $slug !== '' ? Str::slug($slug) : Str::slug($title);

        return $slug !== '' ? $slug : 'listing';
    }

    protected function makeUniqueSlug(string $sourceSlug): string
    {
        $candidate = $sourceSlug;
        $suffix = 1;

        while (Listing::where('slug', $candidate)->exists()) {
            $candidate = $sourceSlug . '-' . $suffix;
            $suffix++;
        }

        return $candidate;
    }

    protected function normalizeKey(?string $value): string
    {
        $value = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = Str::lower($value);
        $value = preg_replace('/[^a-z0-9]+/u', ' ', $value);
        return trim((string) $value);
    }

    protected function normalizeWhitespace(string $value): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }
}
