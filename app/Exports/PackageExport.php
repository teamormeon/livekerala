<?php

namespace App\Exports;

use App\Models\Package;
use App\Models\PurchasePackage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PackageExport implements FromCollection, WithHeadings
{
    protected $package_id;

    public function __construct($package_id)
    {
        $this->package_id = $package_id;
    }

    public function collection()
    {
        $package_in_array = explode(',', $this->package_id);
        return PurchasePackage::with('get_package')->whereIn('id', $package_in_array)->get();
    }

    public function headings(): array
    {
        return [
            'User ID', 'Package ID', 'Package Name', "Price", 'Purchased Date', 'Expired Date'
        ];
    }

    public function map($package): array
    {
        return [
            $package->user_id,
            $package->package_id,
            $this->getPackageTitle($package),
            $package->price,
            $package->purchase_date,
            $package->expire_date,
        ];
    }

    private function getPackageTitle($package)
    {
        if ($package->get_package) {
            return $package->get_package->title;
        }
        return 'N/A';
    }
}
