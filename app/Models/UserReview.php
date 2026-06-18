<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReview extends Model
{
    use HasFactory;
    protected $table = 'user_reviews';
    protected $guarded = ['id'];
    protected $appends = ['date_formatted'];

    public function review_user_info(){
        return $this->belongsTo(User::class, 'user_id');
    }


    public function getListing(){
        return $this->belongsTo(Listing::class, 'listing_id', 'id');
    }


    public function getDateFormattedAttribute()
    {
        return ($this->created_at)?$this->created_at->format('M d, Y h:i A'):'';
    }
}
