<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryStates extends Model
{
    use HasFactory;
    protected $table = 'states';
    protected $guarded = ['id'];

    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }

    public function cities(){
        return $this->hasMany(CountryCities::class,'state_id');
    }
}
