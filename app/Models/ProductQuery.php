<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductQuery extends Model
{
    use HasFactory;
    protected $table = 'product_queries';
    protected $guarded = ['id'];

    public function get_user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function get_client(){
        return $this->belongsTo(User::class, 'client_id', 'id');
    }

    public function get_listing(){
        return $this->belongsTo(Listing::class, 'listing_id', 'id');
    }

    public function get_product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function replies()
    {
        return $this->hasMany(ProductReply::class, 'product_query_id', 'id');
    }

    public function unseenReplies()
    {
        return $this->hasMany(ProductReply::class, 'product_query_id', 'id')->where('status', 0)->where('client_id', auth()->id());
    }
}
