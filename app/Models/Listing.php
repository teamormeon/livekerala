<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Listing extends Model
{
    use HasFactory;
    protected $table = 'listings';
    protected $guarded = ['id'];

    protected $appends = [
        'avgRating'
    ];

    protected $casts = [
        'category_id' => 'array',
    ];

    public function get_user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function get_place()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function get_cities()
    {
        return $this->belongsTo(CountryCities::class, 'city_id');
    }

    public function get_listing_images()
    {
        return $this->hasMany(ListingImage::class, 'listing_id');
    }

    public function get_products()
    {
        return $this->hasMany(Product::class, 'listing_id');
    }

    public function get_listing_amenities()
    {
        return $this->hasMany(ListingAmenity::class, 'listing_id');
    }

    public function get_business_hour()
    {
        return $this->hasMany(BusinessHour::class, 'listing_id');
    }

    public function get_reviews()
    {
        return $this->hasMany(UserReview::class, 'listing_id');
    }

    public function scopeReviews()
    {
        $query = $this->get_reviews()
            ->selectRaw('count(id) AS total')
            ->selectRaw('AVG(rating) AS average')->toBase()->get()->toArray();
        return $query;
    }

    public function form()
    {
        return $this->hasOne(DynamicForm::class, 'listing_id');
    }

    public function getTotalRatingAttribute()
    {
        return $this->get_reviews()->sum('rating');
    }

    public function getAvgRatingAttribute()
    {
        return $this->get_reviews()->avg('rating');
    }

    public function get_package()
    {
        return $this->belongsTo(PurchasePackage::class, 'purchase_package_id');
    }

    public function get_social_info()
    {
        return $this->hasMany(WebsiteAndSocial::class, 'listing_id');
    }

    public function getFavourite()
    {
        $clientId = Auth::check() ? Auth::user()->id : null;
        return $this->hasMany(Favourite::class, 'listing_id')
            ->when($clientId, function ($query) use ($clientId) {
                return $query->where('client_id', $clientId);
            });
    }

    public function listingImages()
    {
        return $this->hasMany(ListingImage::class, 'listing_id');
    }

    public function listingSeo()
    {
        return $this->hasOne(ListingSeo::class, 'listing_id');
    }

    public function listingAnalytics()
    {
        return $this->hasMany(Analytics::class, 'listing_id');
    }

    public function listingClaims()
    {
        return $this->hasMany(ClaimBusiness::class, 'listing_id');
    }

    public function allWishlists()
    {
        return $this->hasMany(Favourite::class, 'listing_id');
    }

    public function productQueries()
    {
        return $this->hasMany(ProductQuery::class, 'listing_id');
    }

    public function listingViews()
    {
        return $this->hasMany(Viewer::class, 'listing_id');
    }

    public function getCategories(){
        return ListingCategory::with('details')->whereIn('id', $this->category_id)->get();
    }

 /*   public function getCategoriesName(){
        return implode(" , ", ListingCategoryDetails::whereIn('listing_category_id', $this->category_id)->get()->pluck('name')->toArray());
    }*/

    protected static array $categoryNamesCache = [];
    public function getCategoriesName(): string
    {
        $categoryIds = is_array($this->category_id) ? $this->category_id : json_decode($this->category_id, true);

        if (!is_array($categoryIds) || empty($categoryIds)) {
            return '';
        }
        if (empty(static::$categoryNamesCache)) {
            static::$categoryNamesCache = ListingCategoryDetails::pluck('name', 'listing_category_id')->toArray();
        }
        $categoryNames = array_intersect_key(static::$categoryNamesCache, array_flip($categoryIds));
        return implode(' , ', $categoryNames);
    }


}
