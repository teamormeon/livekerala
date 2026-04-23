<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteAndSocial extends Model
{
    use HasFactory;
    protected $table = 'website_and_socials';
    protected $guarded = ['id'];
}
