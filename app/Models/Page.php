<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class Page extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'template_name', 'custom_link', 'page_title', 'meta_title', 'meta_keywords', 'meta_description', 'og_description', 'meta_robots',
        'meta_image', 'meta_image_driver', 'breadcrumb_image', 'breadcrumb_image_driver', 'breadcrumb_status', 'type', 'status', 'is_breadcrumb_img'];

    protected $casts = ['meta_keywords' => 'object'];

    public function details()
    {
        return $this->hasOne(PageDetail::class, 'page_id', 'id');
    }

    public function manyDetails()
    {
        return $this->hasMany(PageDetail::class, 'page_id', 'id');
    }

    public function getLanguageEditClass($languageId)
    {
        return $this->manyDetails?->contains('language_id',$languageId)
            ? 'bi-check2'
            : 'bi-pencil';
    }

 /*   public function getLanguageEditClass($id, $languageId){
        return DB::table('page_details')->where(['page_id' => $id, 'language_id' => $languageId])->exists() ? 'bi-check2' : 'bi-pencil';
    }*/

    public function getMetaRobots()
    {
        return explode(",", $this->meta_robots);
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(function ($page) {
            if ($page->wasChanged('details')) {
                Artisan::call('cache:clear');
            }
        });
        static::deleting(function () {
            Artisan::call('cache:clear');
        });
    }

}
