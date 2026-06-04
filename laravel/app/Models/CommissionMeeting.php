<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionMeeting extends Model
{
    protected $fillable = ['commission_id', 'title', 'held_at', 'summary'];
    protected $casts = ['held_at' => 'date'];

    public function commission(): BelongsTo { return $this->belongsTo(Commission::class); }
}
