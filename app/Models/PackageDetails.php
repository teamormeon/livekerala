<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageDetails extends Model
{
    use HasFactory, Translatable;
    protected $table = 'package_details';
    protected $guarded = ['id'];

    protected $casts = [
        'details' => 'object'
    ];

    public function package(){
        return $this->belongsTo(Package::class, 'package_id');
    }
}
