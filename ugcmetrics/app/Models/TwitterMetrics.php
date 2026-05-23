<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwitterMetrics extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_profile_id',
        'date',
        'followers',
        'following',
        'tweets',
        'listed',
        'likes',
        'comments',
        'retweets',
        'engagement_rate',
        'impressions',
        'profile_visits',
    ];

    public function socialProfile()
    {
        return $this->belongsTo(SocialProfile::class);
    }
}