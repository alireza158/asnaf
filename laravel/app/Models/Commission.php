<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commission extends Model
{
    protected $fillable = ['slug', 'title', 'summary', 'content', 'sort_order'];

    public function meetings(): HasMany { return $this->hasMany(CommissionMeeting::class); }
}
