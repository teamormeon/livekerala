<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageExpiryCron extends Model
{
    use HasFactory;
    protected $table = 'package_expiry_crons';
    protected $guarded = ['id'];
    protected $casts = ['before_expiry_date'];
}
