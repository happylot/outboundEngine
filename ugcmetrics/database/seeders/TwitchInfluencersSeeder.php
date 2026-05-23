<?php

namespace Database\Seeders;

use App\Models\Influencer;
use App\Models\Platform;
use App\Models\SocialProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Client\RequestException;

class TwitchInfluencersSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pendientes = [];

        // 1. Recupera o crea la plataforma Twitch
        $platform = Platform::firstOrCreate(
            ['name' => 'Twitch'],
            [
                'description' => 'Twitch is a live streaming platform for gamers and content creators.',
                'base_url'    => 'https://www.twitch.tv/'
            ]
        );

        // 2. Define los usuarios de Twitch y sus URLs de avatar
        $twitchUsers = [
            // 🇪🇸  España
            [
                'username' => 'ibai',
                'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/574228be-01ef-4eab-b43e-651e6b0877b5-profile_image-300x300.png',
            ],
            [
                'username' => 'thegrefg',
                'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/ae756e73-8f9f-4e1b-8d5c-f88462a97262-profile_image-300x300.png',
            ],

            // 🇺🇸  Estados Unidos
            [
                'username' => 'ninja',
                'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/34a3e1ae-d38d-479b-9b93-a2c3e79d4e03-profile_image-300x300.png',
            ],
            [
                'username' => 'tarik',
                'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/a3c8c110-ae60-4e5e-bdac-773d7aac18f0-profile_image-300x300.png',
            ],
            [
                'username' => 'sodapoppin',
                'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/d6f5094d-7de0-41b3-98d3-787ce7f1ce76-profile_image-300x300.png',
            ],
            [
                'username' => 'summit1g',
                'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/4c8ad5ac-7e4a-49d7-89fb-d1bac06b99e8-profile_image-300x300.png',
            ],
            [
                'username' => 'lirik',
                'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/77c29e8d-0e3e-4a4b-bb7a-379ebaea6137-profile_image-300x300.png',
            ],
            [
                'username' => 'loltyler1',
                'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/0501af24-e0a3-4b3e-b2be-f3923bc082c3-profile_image-300x300.png',
            ],

            // 🇨🇦  Canadá / Marruecos
            [
                'username' => 'xqc',
                'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/e733dffe-e132-4e11-a07d-936ab650ad1b-profile_image-300x300.png',
            ],
            [
                'username' => 'pokimane',
                'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/695b6d97-d010-4e32-87fd-b9a1494936d6-profile_image-300x300.png',
            ],
            [
                'username' => 'shroud',
                'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/f4d3d8e7-7b4d-45f5-a4da-87d65e1b85d1-profile_image-300x300.png',
            ],

            // 🇧🇷  Brasil
            [
                'username' => 'gaules',
                'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/0defff17-8c73-4e1d-8c11-50637e2b2fc7-profile_image-300x300.png',
            ],

            // 🇯🇵  Japón
            [
                'username' => 'fps_shaka',
                'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/c8ad1079-7253-4aa0-a467-9de6f6578718-profile_image-300x300.png',
            ],
            [
                'username' => 'stylishnoob4',
                'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/63218b8f-ec56-477d-819a-3142ab24c535-profile_image-300x300.png',
            ],

            // 🇦🇷  Argentina
            [
                'username' => 'coscu',
                'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/383cbf85-56a2-4bd2-9330-fa3ab4db4eb1-profile_image-300x300.png',
            ],
            [
        'username' => 'th3antonio',
        'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/placeholder-profile_image-300x300.png',
    ],  [
        'username' => 'snoodyboo',
        'avatar'   => 'https://static-cdn.jtvnw.net/jtv_user_pictures/snoodyboo-profile_image-300x300.png',
    ],
        ];

        foreach ($twitchUsers as $user) {
            // Verificar si el influencer ya existe por username
            $influencer = Influencer::where('username', $user['username'])->first();

            // Si no existe, crearlo
            if (!$influencer) {
                $influencer = Influencer::create([
                    'name'     => ucfirst($user['username']),
                    'username' => $user['username'],
                    'bio'      => '',  // Ajusta si quieres una biografía
                    // 'profile_picture_url' => '' // Lo podrías rellenar tras descargar la imagen
                ]);
            }

            // Verificar si ya existe un perfil social para este influencer y esta plataforma
            $socialProfile = SocialProfile::where('influencer_id', $influencer->id)
                                         ->where('platform_id', $platform->id)
                                         ->first();

            // Solo crear el perfil social si no existe
            if (!$socialProfile) {
                SocialProfile::create([
                    'influencer_id' => $influencer->id,
                    'platform_id'   => $platform->id,
                    'username'      => $user['username'],
                    'profile_url'   => "https://www.twitch.tv/{$user['username']}",
                ]);
            }

            /* ────────────────────────────────────────────────────────────────
            |  SINCRONIZA una vez mediante el controlador para que se guarde
            |  avatar, followers, streams, etc.  – no afecta a la semilla si
            |  la API de Twitch devuelve error (lo registramos y seguimos).
            *────────────────────────────────────────────────────────────────*/
            try {
                app(\App\Http\Controllers\TwitchController::class)
    ->syncProfile($user['username']);
   // ← esta es la línea nueva
            } catch (\Throwable $e) {
                $this->command->warn(
                    "No se pudo sincronizar {$user['username']}: {$e->getMessage()}"
                );
                $pendientes[] = $user['username'];
            }

            $this->command->info("Created/Updated Twitch influencer: {$user['username']}");
        }

        /* ─────────────  re‑intento diferido  ───────────── */
        if ($pendientes) {
            $this->command->info(
                'Esperando 20 s y reintentando: '.implode(', ', $pendientes)
            );
            sleep(20);

            foreach ($pendientes as $userName) {
                try {
                    app(\App\Http\Controllers\TwitchController::class)
                    ->syncProfile($user['username']);                                    $this->command->info("Reintento OK: {$userName}");
                } catch (\Throwable $e) {
                    $this->command->warn("Reintento fallido {$userName}: ".$e->getMessage());
                }
            }
        }
    }
}
