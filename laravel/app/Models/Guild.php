<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guild extends Model
{
    protected $fillable = ['category_id', 'slug', 'title', 'chairman', 'phone', 'complaints_enabled', 'features', 'members_count', 'summary', 'content'];
    protected $casts = ['complaints_enabled' => 'boolean', 'features' => 'array'];

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function members(): HasMany { return $this->hasMany(GuildMember::class); }
    public function complaints(): HasMany { return $this->hasMany(Complaint::class); }
    public function pageBlocks(): HasMany { return $this->hasMany(GuildPageBlock::class); }
}
