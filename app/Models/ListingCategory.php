<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ListingCategory extends Model
{
    use HasFactory;
    protected $table = 'listing_categories';
    protected $guarded = ['id'];

    public function language()
    {
        return $this->hasMany(Language::class, 'language_id', 'id');
    }

    public function details()
    {
        return $this->hasOne(ListingCategoryDetails::class);
    }

    public function get_listings()
    {
        return $this->hasMany(Listing::class, 'category_id', 'id')->where('status', 1)->where('is_active', 1);
    }

    public function getCategoryCount()
    {
        return Listing::whereJsonContains('category_id', json_encode($this->id))->where('status', 1)->where('is_active', 1)->count();
    }




}
