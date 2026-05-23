<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeedingScenario extends Model
{
    use HasFactory;

    protected $table = 'seeding_scenarios';

    protected $fillable = [
        'title',
        'target_link',
        'brand_name',
        'context',
    ];

    public function templates(): HasMany
    {
        return $this->hasMany(SeedingTemplate::class, 'scenario_id');
    }
}
