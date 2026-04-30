<?php

namespace App\Console\Commands;

use App\Models\PurchasePackage;
use App\Models\User;
use App\Services\LiveKeralaDirectoryImportService;
use Illuminate\Console\Command;

class ImportLiveKeralaDirectoryListings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-livekerala-listings
                            {--owner=abhilash@ormeon.com : Listing owner email}
                            {--source=https://www.livekerala.com : Source directory base URL}
                            {--limit=0 : Limit imported listings for testing}
                            {--max-pages=250 : Maximum source pages to crawl while discovering listings}
                            {--dry-run : Discover and validate listings without saving them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import public listings from livekerala.com and assign them to an owner with the active free package';

    /**
     * Execute the console command.
     */
    public function handle(LiveKeralaDirectoryImportService $importService): int
    {
        $ownerEmail = (string) $this->option('owner');
        $owner = User::where('email', $ownerEmail)->first();

        if (!$owner) {
            $this->error('No user found for owner email: ' . $ownerEmail);
            return self::FAILURE;
        }

        $purchasePackage = PurchasePackage::with('get_package')
            ->where('user_id', $owner->id)
            ->where('status', 1)
            ->get()
            ->first(function (PurchasePackage $package) {
                $price = $package->price ?? optional($package->get_package)->price;
                return $price === null || (float) $price === 0.0;
            });

        if (!$purchasePackage) {
            $this->error('No active free package found for ' . $ownerEmail . '. Create or assign a free package to this user first, then rerun the import.');
            return self::FAILURE;
        }

        $this->info('Starting LiveKerala import for ' . $ownerEmail . ' using purchase package #' . $purchasePackage->id . '.');

        $results = $importService->import($owner, $purchasePackage, [
            'source_url' => (string) $this->option('source'),
            'limit' => (int) $this->option('limit'),
            'max_pages' => (int) $this->option('max-pages'),
            'dry_run' => (bool) $this->option('dry-run'),
        ]);

        $this->newLine();
        $this->info('Discovery complete.');
        $this->line('Discovered: ' . $results['discovered']);
        $this->line('Imported: ' . $results['imported']);
        $this->line('Skipped: ' . $results['skipped']);
        $this->line('Failed: ' . $results['failed']);

        if (!empty($results['errors'])) {
            $this->newLine();
            $this->warn('Skipped / failed items:');
            foreach (array_slice($results['errors'], 0, 50) as $error) {
                $this->line('- ' . $error);
            }

            if (count($results['errors']) > 50) {
                $this->line('- ...and ' . (count($results['errors']) - 50) . ' more');
            }
        }

        return ($results['failed'] ?? 0) > 0 ? self::FAILURE : self::SUCCESS;
    }
}
