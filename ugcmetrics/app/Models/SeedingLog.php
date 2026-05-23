<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SeedingLog extends Model
{
    use HasFactory;

    protected $table = 'seeding_logs';

    protected $fillable = [
        'seeder_account_id',
        'action_type',
        'target_url',
        'content_type',
        'content',
        'status',
        'response_metadata',
        'logged_at',
    ];

    protected $casts = [
        'response_metadata' => 'array',
        'logged_at' => 'datetime',
    ];

    public function seederAccount(): BelongsTo
    {
        return $this->belongsTo(SeederAccount::class, 'seeder_account_id');
    }

    public function incident(): HasOne
    {
        return $this->hasOne(SeedingIncident::class, 'trigger_action_id');
    }
}
