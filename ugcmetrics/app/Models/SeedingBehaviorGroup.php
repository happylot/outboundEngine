<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeedingBehaviorGroup extends Model
{
    use HasFactory;

    protected $table = 'seeding_behavior_groups';

    protected $fillable = [
        'name',
        'daily_limit',
        'min_interval_minutes',
        'max_interval_minutes',
        'description',
    ];

    public function seederAccounts(): HasMany
    {
        return $this->hasMany(SeederAccount::class, 'behavior_group_id');
    }
}
