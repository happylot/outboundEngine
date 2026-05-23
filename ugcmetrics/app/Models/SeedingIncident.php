<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeedingIncident extends Model
{
    use HasFactory;

    protected $table = 'seeding_incidents';

    protected $fillable = [
        'seeder_account_id',
        'incident_type',
        'trigger_action_id',
        'description',
        'resolved_at',
        'recovery_time_minutes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function seederAccount(): BelongsTo
    {
        return $this->belongsTo(SeederAccount::class, 'seeder_account_id');
    }

    public function triggerAction(): BelongsTo
    {
        return $this->belongsTo(SeedingLog::class, 'trigger_action_id');
    }
}
