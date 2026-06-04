<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuildMember extends Model
{
    protected $fillable = ['guild_id', 'name', 'mobile', 'business_name', 'license_number', 'sms_enabled'];
    protected $casts = ['sms_enabled' => 'boolean'];

    public function guild(): BelongsTo { return $this->belongsTo(Guild::class); }
}
