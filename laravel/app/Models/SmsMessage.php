<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsMessage extends Model
{
    protected $fillable = ['guild_id', 'sent_by', 'recipient_type', 'recipient_mobile', 'body', 'status'];

    public function guild(): BelongsTo { return $this->belongsTo(Guild::class); }
    public function sender(): BelongsTo { return $this->belongsTo(User::class, 'sent_by'); }
}
