<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    use HasFactory;
    protected $table = 'followers';
    protected $guarded = ['id'];

    public function get_follwer_user(){
        return $this->belongsTo(User::class, 'following_id');
    }

    public function get_following_user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
