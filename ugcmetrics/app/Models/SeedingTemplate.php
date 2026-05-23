<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeedingTemplate extends Model
{
    use HasFactory;

    protected $table = 'seeding_templates';

    protected $fillable = [
        'scenario_id',
        'angle',
        'content',
    ];

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(SeedingScenario::class, 'scenario_id');
    }
}
