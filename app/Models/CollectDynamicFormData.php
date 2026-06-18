<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectDynamicFormData extends Model
{
    use HasFactory;
    protected $table = 'collect_dynamic_form_data';
    protected $guarded = ['id'];
    protected $casts = ['input_form' => 'object'];

}
