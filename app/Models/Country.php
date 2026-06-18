<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table = 'countries';
    protected $guarded = ['id'];

    public function state(){
        return $this->hasMany(CountryStates::class,'country_id');
    }

    public function city(){
        return $this->hasMany(CountryCities::class,'country_id');
    }
}
