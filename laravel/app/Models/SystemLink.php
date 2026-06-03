<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLink extends Model
{
    protected $table = 'systems';
    protected $fillable = ['slug', 'title', 'url', 'description', 'is_external', 'enabled'];
    protected $casts = ['is_external' => 'boolean', 'enabled' => 'boolean'];
}
