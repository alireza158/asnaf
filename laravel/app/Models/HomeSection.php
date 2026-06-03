<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    protected $fillable = ['key', 'title', 'enabled', 'sort_order', 'settings'];
    protected $casts = ['enabled' => 'boolean', 'settings' => 'array'];
}
