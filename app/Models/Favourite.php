<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory;
    protected $table = 'favourites';
    protected $guarded = ['id'];

    public function get_user(){
        return $this->belongsTo(User::class, 'client_id', 'id');
    }

    public function get_listing(){
        return $this->belongsTo(Listing::class, 'listing_id', 'id');
    }

    public function get_purchase_package(){
        return $this->belongsTo(PurchasePackage::class, 'purchase_package_id');
    }
}
