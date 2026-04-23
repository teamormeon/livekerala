<?php

namespace App\Traits;

use App\Models\Blog;
use App\Models\ContentDetails;
use App\Models\Country;
use App\Models\Listing;
use App\Models\ListingCategory;
use Illuminate\Support\Facades\DB;

trait Frontend
{
    protected function getSectionsData($sections, $content, $selectedTheme)
    {
        if ($sections == null) {
            $data = ['support' => $content,];
            return view("themes.$selectedTheme.support", $data)->toHtml();
        }

        $contentData = ContentDetails::with('content')
            ->whereHas('content', function ($query) use ($sections) {
                $query->whereIn('name', $sections);
            })
            ->get();


        foreach ($sections as $section) {
            $singleContent = $contentData->where('content.theme', $selectedTheme)->where('content.name', $section)->where('content.type', 'single')->first() ?? [];
            if ($section == 'blog') {
                $data[$section] = [
                    'single' => $singleContent ? collect($singleContent->description ?? [])->merge($singleContent->content->only('media')) : [],
                    'popularBlogs' => Blog::with(['details', 'category'])->where('status', 1)->take(3)->latest()->get()
                ];
            } elseif ($section == 'hero'){
                $listingCategories = ListingCategory::with('details')
                    ->where('status', 1)
                    ->get();
                $data[$section] = [
                    'single' => $singleContent ? collect($singleContent->description ?? [])->merge($singleContent->content->only('media')) : [],
                    'all_categories' => $listingCategories->sortByDesc('id'),
                    'all_places' => Country::select('id', 'name')->where('status', 1)->orderBy('name', 'ASC')->toBase()->get(),
                    'uniqueCities' => Listing::with('get_cities')->where('city_id', '!=', null)->get()->pluck('get_cities'),
                    'highlights_categories' => $listingCategories->random(4),
                ];
            }elseif ($section == 'listing'){
                $data[$section] = [
                    'single' => $singleContent ? collect($singleContent->description ?? [])->merge($singleContent->content->only('media')) : [],
                    'popularListings' => Listing::with(['get_reviews', 'get_user'])
                        ->where('status', 1)->where('is_active', 1)
                        ->withCount('getFavourite')
                        ->inRandomOrder()
                        ->get()
                        ->sortByDesc(function ($item) {
                            return $item->avgRating;
                        })->take(4)
                ];
            } elseif ($section == 'listing_categories'){
                $categoryIds = array_count_values(Listing::where('status',1)->where('is_active',1)->pluck('category_id')->flatten()->toArray());
                arsort($categoryIds);
                $sliceCategoryIds = array_keys(array_slice($categoryIds, 0, 8, true));

                if (!empty($sliceCategoryIds)) {
                    $popularCategories = ListingCategory::with('details')
                        ->whereIn('id', $sliceCategoryIds)
                        ->orderByRaw('FIELD(id, ' . implode(',', $sliceCategoryIds) . ')')
                        ->get();
                } else {
                    $popularCategories = collect(); // Return an empty collection
                }
                $data[$section] = [
                    'single' => $singleContent ? collect($singleContent->description ?? [])->merge($singleContent->content->only('media')) : [],


                    'popularCategories' => $popularCategories
                ];
            } else {
                $multipleContents = $contentData->where('content.theme', $selectedTheme)->where('content.name', $section)->where('content.type', 'multiple')->values()->map(function ($multipleContentData) {
                    return collect($multipleContentData->description)->merge($multipleContentData->content->only('media'));
                });

                $data[$section] = [
                    'single' => $singleContent ? collect($singleContent->description ?? [])->merge($singleContent->content->only('media')) : [],
                    'multiple' => $multipleContents
                ];
            }

            $replacement = view("themes.{$selectedTheme}.sections.{$section}", $data)->toHtml();

            $content = str_replace('<div class="custom-block" contenteditable="false"><div class="custom-block-content">[[' . $section . ']]</div>', $replacement, $content);
            $content = str_replace('<span class="delete-block">×</span>', '', $content);
            $content = str_replace('<span class="up-block">↑</span>', '', $content);
            $content = str_replace('<span class="down-block">↓</span></div>', '', $content);
            $content = str_replace('<p><br></p>', '', $content);
        }

        return $content;
    }
}
