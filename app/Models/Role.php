<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'permission' => 'array'
    ];

    public function roleUsers()
    {
        return $this->hasMany(Admin::class, 'role_id');
    }
}
