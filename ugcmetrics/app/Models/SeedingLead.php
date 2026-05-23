<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\ZenifySyncService;

class SeedingLead extends Model
{
    use HasFactory;

    protected $table = 'seeding_leads';

    protected $fillable = [
        'seeder_account_id',
        'customer_name',
        'customer_uid',
        'platform',
        'profile_url',
        'comment_url',
        'content',
        'phone',
        'email',
        'status',
        'zenify_lead_id',
        'sync_error',
    ];

    public function seederAccount(): BelongsTo
    {
        return $this->belongsTo(SeederAccount::class, 'seeder_account_id');
    }

    /**
     * Synchronize the lead to Zenify CRM.
     */
    public function syncToZenify(): bool
    {
        return app(ZenifySyncService::class)->sync($this);
    }
}
