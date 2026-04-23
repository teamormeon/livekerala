<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchasePackage extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'purchase_packages';
    protected $guarded = ['id'];
    protected $dates = ['purchase_date', 'expire_date', 'last_reminder_at', 'deleted_at'];

    public function get_package(){
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }

    public function get_user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deposit(){
        return $this->belongsTo(Deposit::class, 'deposit_id');
    }
}
