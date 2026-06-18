<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class ManageMenu extends Model
{
    use HasFactory;

    protected $fillable = ['template_name','menu_section', 'menu_items'];

    protected $casts = ['menu_items' => 'array'];


    protected static function boot()
    {
        parent::boot();
        static::saved(function ($menu) {
            if ($menu->wasChanged()) {
                Artisan::call('cache:clear');
            }
        });
        static::deleting(function () {
            Artisan::call('cache:clear');
        });
    }

}
