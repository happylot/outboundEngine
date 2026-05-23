<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboundLead extends Model
{
    protected $fillable = ['name', 'salon_name', 'platform', 'social_id', 'status', 'score', 'enrichment_data', 'generated_message'];

    protected $casts = [
        'enrichment_data' => 'array',
    ];
}
