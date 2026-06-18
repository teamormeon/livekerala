<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
    protected $table = 'packages';
    protected $guarded = ['id'];

    public function language()
    {
        return $this->hasMany(Language::class, 'language_id', 'id');
    }

    public function details()
    {
        return $this->hasOne(PackageDetails::class);
    }

    public function purchasePackages()
    {
        return $this->hasMany(PurchasePackage::class, 'package_id');
    }

    public function isFreePurchase()
    {
        if (!auth()->check()) {
            return 'false';
        }
        $user = auth()->user();
        $purPaks = $this->purchasePackages->where('user_id', $user->id);
        foreach ($purPaks as $pak) {
            if ($pak->price == null && $this->is_multiple_time_purchase == 0) {
                return 'true';
            }
        }
        return 'false';
    }
}
