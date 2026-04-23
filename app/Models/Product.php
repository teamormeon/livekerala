<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $guarded = ['id'];

    public function get_product_image(){
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function getListing(){
        return $this->belongsTo(Listing::class, 'listing_id', 'id');
    }
}
