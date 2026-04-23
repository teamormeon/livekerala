<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryCities extends Model
{
    use HasFactory;
    protected $table = 'cities';
    protected $guarded = ['id'];

    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }
    public function state(){
        return $this->belongsTo(CountryStates::class,'state_id');
    }

    public function getAddress()
    {
        return $this->name . ', ' . $this->state->name . ', ' . $this->state->country->name;
    }
}
