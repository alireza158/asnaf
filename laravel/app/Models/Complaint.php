<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    protected $fillable = ['guild_id', 'tracking_code', 'name', 'mobile', 'body', 'status'];

    public function guild(): BelongsTo { return $this->belongsTo(Guild::class); }
}
