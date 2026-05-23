<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwitterReports extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_profile_id',
        'followers_start',
        'followers_end',
        'year',
        'month',
        'followers_count',
        'tweets_count',
        'likes_count',
        'comments_count',
        'retweets_count',
        'followers_growth',
        'engagement_rate',
        'impressions',
        'profile_visits',
        'mentions_count',
        'hashtag_performance',
    ];

    protected $casts = [
        'hashtag_performance' => 'array',
    ];

    public function socialProfile()
    {
        return $this->belongsTo(SocialProfile::class);
    }
}