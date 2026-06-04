<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuildPageBlock extends Model
{
    protected $fillable = ['guild_id', 'block_type', 'title', 'subtitle', 'items', 'enabled', 'sort_order'];
    protected $casts = ['items' => 'array', 'enabled' => 'boolean'];

    public function guild(): BelongsTo
    {
        return $this->belongsTo(Guild::class);
    }
}
