<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimBusinessChating extends Model
{
    use HasFactory;
    protected $table = 'claim_business_chatings';
    protected $guarded = ['id'];

    protected $appends = ['formatted_date','updated_date'];

    public function userable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userable_id');
    }

    public function lastMessage()
    {
        return $this->hasOne(ClaimBusinessChating::class, 'claim_business_id')->latest();
    }


    public function getFormattedDateAttribute()
    {
        return dateTime($this->created_at);
    }
    public function getUpdatedDateAttribute()
    {
        return dateTime($this->updated_at);
    }

}
