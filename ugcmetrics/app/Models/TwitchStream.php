<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwitchStream extends Model
{
    /** @use HasFactory<\Database\Factories\TwitchStreamFactory> */
    use HasFactory;
    
    protected $fillable = [
        'social_profile_id',
        'stream_id',
        'title',
        'game_id', 
        'game_name',
        'stream_url',
        'thumbnail_url',
        'started_at',
        'ended_at',
        'duration_minutes',
        'viewer_count',
        'peak_viewers',
        'average_viewers',
        'followers_gained',
        'chat_messages',
        'is_mature',
        'language',
        'tags',
        'is_sponsored',
    ];

    protected $dates = ['started_at', 'ended_at'];
    
    protected $casts = [
        'tags' => 'array',
        'is_mature' => 'boolean',
        'is_sponsored' => 'boolean',
    ];

    public function socialProfile()
    {
        return $this->belongsTo(SocialProfile::class);
    }
}