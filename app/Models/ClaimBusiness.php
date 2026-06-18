<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimBusiness extends Model
{
    use HasFactory;
    protected $table = 'claim_businesses';
    protected $guarded = ['id'];

    public function get_client(){
        return $this->belongsTo(User::class, 'claim_by_id', 'id');
    }

    public function get_listing(){
        return $this->belongsTo(Listing::class, 'listing_id', 'id');
    }

    public function get_listing_owner(){
        return $this->belongsTo(User::class, 'listing_owner_id', 'id');
    }

    public function claimBusinessChat()
    {
        return $this->hasMany(ClaimBusinessChating::class, 'claim_business_id');
    }
}
