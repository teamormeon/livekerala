<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingSeo extends Model
{
    use HasFactory;
    protected $table = 'listing_seos';
    protected $guarded = ['id'];
}
