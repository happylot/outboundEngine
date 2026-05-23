<?php

namespace App\Console\Commands;

use App\Models\Influencer;
use App\Models\SocialProfile;
use App\Models\Platform;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncRedditInfluencers extends Command
{
    protected $signature = 'reddit:sync-influencers';
    protected $description = 'Sincroniza influences de Reddit en la base de datos';

    public function handle()
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

        $redditPlatform = Platform::where('name', 'Reddit')->first();
        if (!$redditPlatform) {
            $this->error('Plataforma Reddit no encontrada en la tabla de plataformas.');
            Log::error('Reddit platform not found in the platforms table.');
            return;
        }

        foreach ($usernames as $username) {
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

                    $this->info("Usuario de Reddit sincronizado: {$username}");
                    Log::info("Synced Reddit user: {$username}");
                } else {
                    $this->error("Error al sincronizar usuario de Reddit: {$username}");
                    Log::error("Failed to sync Reddit user: {$username}, Response: " . $response->body());
                }
            } catch (\Exception $e) {
                $this->error("Error al sincronizar usuario de Reddit: {$username}, Error: " . $e->getMessage());
                Log::error("Error in SyncRedditInfluencers for user {$username}: " . $e->getMessage());
            }
        }

        $this->info('¡Influencers de Reddit sincronizados con éxito!');
    }
}