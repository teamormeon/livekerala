<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;
    protected $table = 'amenities';
    protected $guarded = ['id'];

    public function language()
    {
        return $this->hasMany(Language::class, 'language_id', 'id');
    }

    public function details(){
        return $this->hasOne(AmenityDetails::class);
    }
}
