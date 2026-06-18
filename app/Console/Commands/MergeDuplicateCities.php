<?php

namespace App\Console\Commands;

use App\Models\CountryCities;
use App\Models\Listing;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MergeDuplicateCities extends Command
{
    protected $signature = 'cities:merge-duplicates
                            {--country_id= : Only process one country/district id}
                            {--state_id= : Only process one state id}
                            {--dry-run : Show what would change without saving}';

    protected $description = 'Merge duplicate cities safely by repointing listings to one kept city record.';

    public function handle(): int
    {
        $cities = CountryCities::query()
            ->select('id', 'country_id', 'state_id', 'name', 'status', 'created_at')
            ->when($this->option('country_id'), fn($query, $countryId) => $query->where('country_id', $countryId))
            ->when($this->option('state_id'), fn($query, $stateId) => $query->where('state_id', $stateId))
            ->orderBy('country_id')
            ->orderBy('state_id')
            ->orderBy('id')
            ->get();

        $duplicateGroups = $cities
            ->groupBy(fn(CountryCities $city) => $city->country_id.'|'.$city->state_id.'|'.$this->normalizeCityName($city->name))
            ->filter(fn(Collection $group) => $group->count() > 1);

        if ($duplicateGroups->isEmpty()) {
            $this->info('No duplicate cities found.');
            return self::SUCCESS;
        }

        $summary = [
            'groups' => 0,
            'cities_deleted' => 0,
            'listings_repointed' => 0,
        ];

        foreach ($duplicateGroups as $groupKey => $group) {
            [$countryId, $stateId] = explode('|', $groupKey, 3);
            $keeper = $group->sortBy([
                ['status', 'desc'],
                ['created_at', 'asc'],
                ['id', 'asc'],
            ])->first();

            $duplicates = $group->where('id', '!=', $keeper->id)->values();
            $duplicateIds = $duplicates->pluck('id')->all();
            $affectedListings = Listing::whereIn('city_id', $duplicateIds)->count();

            $summary['groups']++;
            $summary['cities_deleted'] += count($duplicateIds);
            $summary['listings_repointed'] += $affectedListings;

            $this->line(sprintf(
                'Duplicate city "%s" in country %s / state %s: keep #%d, merge [%s], repoint %d listing(s).',
                $keeper->name,
                $countryId,
                $stateId,
                $keeper->id,
                implode(', ', $duplicateIds),
                $affectedListings
            ));

            if ($this->option('dry-run')) {
                continue;
            }

            DB::transaction(function () use ($keeper, $duplicateIds) {
                Listing::whereIn('city_id', $duplicateIds)->update(['city_id' => $keeper->id]);
                CountryCities::whereIn('id', $duplicateIds)->delete();
            });
        }

        $this->newLine();

        if ($this->option('dry-run')) {
            $this->info(sprintf(
                'Dry run complete. %d duplicate group(s), %d duplicate city row(s), %d listing(s) would be updated.',
                $summary['groups'],
                $summary['cities_deleted'],
                $summary['listings_repointed']
            ));
        } else {
            $this->info(sprintf(
                'Merged %d duplicate group(s), deleted %d duplicate city row(s), updated %d listing(s).',
                $summary['groups'],
                $summary['cities_deleted'],
                $summary['listings_repointed']
            ));
        }

        return self::SUCCESS;
    }

    protected function normalizeCityName(?string $name): string
    {
        return Str::of((string) $name)
            ->squish()
            ->lower()
            ->toString();
    }
}
