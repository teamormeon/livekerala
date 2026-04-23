<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicForm extends Model
{
    use HasFactory;
    protected $table = 'dynamic_forms';
    protected $guarded = ['id'];
    public $casts = ['input_form' => 'object'];
}
