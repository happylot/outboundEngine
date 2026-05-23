<?php

namespace App\Http\Controllers;

use App\Models\TwitterPost;
use App\Models\TwitterReports;
use App\Models\Platform;
use App\Models\SocialProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TwitterGetTwitterData extends Controller
{
    public function __invoke(Request $request)
    {
        $twitterAccount = SocialProfile::where('platform_id', Platform::where('name', 'Twitter')->first()->id)
            ->orderBy('last_updated', 'asc')
            ->first();

        // Implementar lógica de API de Twitter aquí (similar a Instagram pero con endpoints de Twitter)
        $response = $this->fetchTwitterData($twitterAccount->username);

        // Actualizar métricas
        $twitterAccount->followers_count = $response['followers_count'];
        $twitterAccount->last_updated = now();

        $twitterReport = TwitterReports::updateOrCreate([
            'social_profile_id' => $twitterAccount->id,
            'year' => now()->year,
            'month' => now()->month,
        ], [
            'followers_start' => $twitterReport->followers_start ?? $twitterAccount->followers_count,
            'followers_end' => $twitterAccount->followers_count
        ]);

        // Procesar tweets
        foreach ($response['tweets'] as $tweet) {
            TwitterPost::updateOrCreate(
                ['post_id' => $tweet['id']],
                [
                    'social_profile_id' => $twitterAccount->id,
                    'content' => $tweet['text'],
                    'published_at' => Carbon::parse($tweet['created_at']),
                    'likes' => $tweet['like_count'],
                    'comments' => $tweet['reply_count'],
                    'retweets' => $tweet['retweet_count'],
                    'engagement_rate' => ($tweet['like_count'] + $tweet['reply_count']) / $twitterAccount->followers_count * 100
                ]
            );
        }

        // Actualizar engagement rate promedio
        $twitterAccount->engagement_rate = DB::selectOne(
            'SELECT AVG(engagement_rate) AS avg_rate 
             FROM (SELECT engagement_rate 
                   FROM twitter_posts 
                   WHERE social_profile_id = ? 
                   ORDER BY published_at DESC 
                   LIMIT 30) AS subquery', 
            [$twitterAccount->id]
        )->avg_rate;
        
        $twitterAccount->save();

        return response()->json(['status' => 'success']);
    }

    private function fetchTwitterData($username)
    {
        // Implementar llamada real a la API de Twitter
        return [
            'followers_count' => 0,
            'tweets' => []
        ];
    }
}