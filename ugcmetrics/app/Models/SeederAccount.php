<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeederAccount extends Model
{
    use HasFactory;

    protected $table = 'seeder_accounts';

    protected $fillable = [
        'username',
        'platform',
        'profile_url',
        'age_group',
        'device_id',
        'behavior_group_id',
        'status',
        'last_active_at',
        'cooldown_until',
        'notes',
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
        'cooldown_until' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(SeedingDevice::class, 'device_id');
    }

    public function behaviorGroup(): BelongsTo
    {
        return $this->belongsTo(SeedingBehaviorGroup::class, 'behavior_group_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(SeedingLog::class, 'seeder_account_id');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(SeedingIncident::class, 'seeder_account_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(SeedingLead::class, 'seeder_account_id');
    }

    public function isUnderCooldown(): bool
    {
        if ($this->status === 'cooldown' && $this->cooldown_until && $this->cooldown_until->isFuture()) {
            return true;
        }
        return false;
    }

    public function getDailyActionCount(): int
    {
        return $this->logs()
            ->where('logged_at', '>=', now()->startOfDay())
            ->where('status', '!=', 'checkpoint_triggered')
            ->count();
    }
}
