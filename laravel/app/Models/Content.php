<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Content extends Model
{
    protected $fillable = ['guild_id', 'category_id', 'slug', 'type', 'title', 'summary', 'content', 'status', 'important', 'published_at', 'approved_by', 'gallery', 'video_type', 'video_url'];
    protected $casts = ['important' => 'boolean', 'gallery' => 'array', 'published_at' => 'datetime'];

    public function guild(): BelongsTo { return $this->belongsTo(Guild::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
}
