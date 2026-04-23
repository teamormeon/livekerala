<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingAmenity extends Model
{
    use HasFactory;
    protected $table = 'listing_amenities';
    protected $guarded = ['id'];

    public function get_amenity(){
        return $this->belongsTo(Amenity::class, 'amenity_id');
    }
}
