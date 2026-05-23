<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwitterPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_profile_id',
        'post_id',
        'content',
        'published_at',
        'likes',
        'comments',
        'retweets',
        'views',
        'engagement_rate',
        'media_urls',
        'hashtags',
        'mentions',
        'is_retweet',
        'is_reply',
        'language',
    ];

    protected $casts = [
        'media_urls' => 'array',
        'hashtags' => 'array',
        'mentions' => 'array',
        'published_at' => 'datetime',
    ];

    public function socialProfile()
    {
        return $this->belongsTo(SocialProfile::class);
    }
}