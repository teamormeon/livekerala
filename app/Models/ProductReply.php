<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReply extends Model
{
    use HasFactory;
    protected $table = 'product_replies';
    protected $guarded = ['id'];

    protected $appends = ['sent_at'];

    public function getSentAtAttribute(){
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    public function get_user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function get_client(){
        return $this->belongsTo(User::class, 'client_id');
    }
}
