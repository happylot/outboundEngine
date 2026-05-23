<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwitchMetrics extends Model
{
    /** @use HasFactory<\Database\Factories\TwitchMetricsFactory> */
    use HasFactory;
    
    protected $fillable = [
        'social_profile_id', 
        'date', 
        'followers', 
        'subscribers',
        'total_views',
        'average_viewers',
        'peak_viewers',
        'hours_streamed',
        'hours_watched',
        'stream_count',
        'chat_messages',
        'bits_earned',
        'subscription_revenue',
        'bits_revenue',
        'donation_revenue',
        'total_revenue',
    ];

    protected $dates = ['date'];

    protected $casts = [
        'date' => 'date',
        'is_live' => 'boolean',
        'extra_data' => 'array',
    ];

    public function socialProfile()
    {
        return $this->belongsTo(SocialProfile::class);
    }
}