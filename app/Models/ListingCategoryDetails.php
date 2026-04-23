<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingCategoryDetails extends Model
{
    use HasFactory, Translatable;
    protected $table = 'listing_category_details';
    protected $guarded = ['id'];

    protected $casts = [
        'details' => 'object'
    ];

    public function category(){
        return $this->belongsTo(ListingCategory::class, 'listing_category_id');
    }
}
