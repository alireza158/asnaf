<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePage extends Model
{
    protected $fillable = ['slug', 'icon', 'title', 'summary', 'content', 'status', 'sort_order'];
}
