<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeedingDevice extends Model
{
    use HasFactory;

    protected $table = 'seeding_devices';

    protected $fillable = [
        'device_name',
        'device_model',
        'os_version',
        'connection_type',
        'proxy_info',
        'status',
        'notes',
    ];

    public function seederAccounts(): HasMany
    {
        return $this->hasMany(SeederAccount::class, 'device_id');
    }
}
