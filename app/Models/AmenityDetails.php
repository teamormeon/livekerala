<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmenityDetails extends Model
{
    use HasFactory, Translatable;
    protected $table = 'amenity_details';
    protected $guarded = ['id'];

    protected $casts = [
        'details' => 'object'
    ];

    public function amenity(){
        return $this->belongsTo(Amenity::class, 'amenity_id');
    }

}
