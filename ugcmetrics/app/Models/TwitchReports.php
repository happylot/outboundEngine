<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwitchReports extends Model
{
    /** @use HasFactory<\Database\Factories\TwitchReportsFactory> */
    use HasFactory;
    
    protected $fillable = [
        'social_profile_id',
        'year',
        'month',
        'followers_start',
        'followers_end',
        'growth_rate',
        'subscribers_average',
        'average_viewers',
        'peak_viewers',
        'hours_streamed',
        'streams_per_week',
        'chat_engagement', // mensajes por minuto promedio
        'streams_count',
        'game_distribution', 
        'estimated_monthly_revenue_min',
        'estimated_monthly_revenue_max',
        'estimated_sponsor_value_min',
        'estimated_sponsor_value_max',
        'estimated_sponsor_value_optimal',
        'top_categories', // JSON con las categorías más usadas
    ];

    protected $casts = [
        'top_categories' => 'array',
        'game_distribution' => 'array',
    ];

    public function socialProfile()
    {
        return $this->belongsTo(SocialProfile::class);
    }

    public function getMonthNameAttribute()
    {
        return \Carbon\Carbon::create()->month($this->month)->locale('es_ES')->monthName;
    }
}