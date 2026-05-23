<?php

namespace App\Http\Controllers;

use App\Models\Influencer;
use App\Models\SocialProfile;
use App\Models\Platform;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RedditController extends Controller
{
    public function index()
    {
        $usernames = [
            'spez',
            'GallowBoob',
            'shittymorph',
            'Unidan',
            'awkwardtheturtle',
            'karmanaut',
            'TheBlueRedditor',
            'nathanfhtagn',
            'pm_me_your_dogs',
            'sarahjeong'
        ];

        return view('reddit.index', compact('usernames'));
    }

    public function show($username)
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'MyLaravelApp/1.0'
            ])->get("https://www.reddit.com/user/{$username}/about.json");

            if ($response->ok() && isset($response['data'])) {
                $info = $response['data'];

                $user = [
                    'username' => $username,
                    'link_karma' => $info['link_karma'] ?? null,
                    'comment_karma' => $info['comment_karma'] ?? null,
                    'total_karma' => $info['total_karma'] ?? (
                        ($info['link_karma'] ?? 0) + ($info['comment_karma'] ?? 0)
                    ),
                    'created_utc' => isset($info['created_utc']) ? date('Y-m-d', $info['created_utc']) : null,
                    'avatar' => $info['icon_img'] ?? null,
                ];

                $redditPlatform = Platform::where('name', 'Reddit')->first();
                if (!$redditPlatform) {
                    Log::error('Reddit platform not found in the platforms table.');
                    return redirect()->route('reddit.index')->with('error', 'Plataforma Reddit no configurada.');
                }

                // Create or update influencer
                $influencer = Influencer::firstOrCreate(
                    ['username' => $username],
                    [
                        'name' => $username,
                        'email' => null,
                        'profile_picture_url' => $user['avatar'],
                        'bio' => null,
                        'location' => null
                    ]
                );

                // Create or update social profile
                SocialProfile::updateOrCreate(
                    [
                        'influencer_id' => $influencer->id,
                        'platform_id' => $redditPlatform->id,
                        'username' => $username
                    ],
                    [
                        'profile_url' => $redditPlatform->base_url . $username,
                        'profile_picture' => $user['avatar'],
                        'followers_count' => 0,
                        'engagement_rate' => 0,
                        'extra_data' => [
                            'link_karma' => $user['link_karma'],
                            'comment_karma' => $user['comment_karma'],
                            'total_karma' => $user['total_karma'],
                            'created_utc' => $user['created_utc']
                        ],
                        'last_updated' => now()
                    ]
                );

                Log::info("Reddit user saved: {$username}");
                return view('reddit.show', compact('user'));
            }

            Log::error("Failed to fetch Reddit user: {$username}, Response: " . $response->body());
            return redirect()->route('reddit.index')->with('error', 'No se pudo obtener la información del usuario.');
        } catch (\Exception $e) {
            Log::error("Error en el RedditController::show for user {$username}: " . $e->getMessage());
            return redirect()->route('reddit.index')->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }
}