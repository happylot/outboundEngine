<?php

namespace Database\Seeders;

use App\Models\Influencer;
use App\Models\Platform;
use App\Models\SocialProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\InstagramMetric;
use App\Models\InstagramMetrics;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class InstagramInfluencersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create the Instagram platform
        $platform = Platform::firstOrCreate(
            ['name' => 'Instagram'],
            [
                'description' => 'Instagram social media platform',
                'base_url' => 'https://www.instagram.com/'
            ]
        );


        $arrayInstagramInfluencers = ['auronplay','mouredev','ibaillanos','mirpratur', 'ironpanda_fitness', 'ironpanda._', 'livetgn', 'adriamarcor', 'nil.sanchhez', 'hogardiez', 'ruaaniii', 'losmaui', 'nuriamgallardo2', 'marinacomes','iamoriolmiro'];

        foreach ($arrayInstagramInfluencers as $username) {
            $data = $this->dataFromAPIInstagram($username);
            // Create the influencer
            $influencer = Influencer::create([
                'name' => $username,
                'bio' => '',
                'username' => $username, // Añadir el campo username que ahora es obligatorio
                'profile_picture_url' => "" // Placeholder URL
            ]);
            
            // Check if data is valid and has the expected structure
            if (is_object($data) && isset($data->data) && isset($data->data->user) && isset($data->data->user->profile_pic_url)) {
                $imgURL = $data->data->user->profile_pic_url;
            } else {
                $this->command->warn("Could not get profile picture for {$username}, using default image");
                $imgURL = "https://placehold.co/150x150.png";  // Default image URL
            }
            $imgPath = 'img/influencer/' . $username . '.jpg';
            Storage::disk('public')->put($imgPath, file_get_contents($imgURL));
            $influencer->profile_picture_url = $imgPath;
            $influencer->save();
            $imgPath = 'img/socialprofile/' . $username . '.jpg';
            Storage::disk('public')->put($imgPath, file_get_contents($imgURL));

            // Create the social profile for Instagram
            SocialProfile::create([
                'influencer_id' => $influencer->id,
                'platform_id' => $platform->id,
                'username' => $username,
                'profile_url' => "https://www.instagram.com/{$username}/",
                'profile_picture' => $imgPath,
            ]);

            $this->command->info("Created influencer: {$username} with Instagram username: {$username}");
        }


        // Usuari amb multiples xarxes socials
        $moure = Influencer::where('name', 'LIKE', '%mouredev%')->first();
        $platform = Platform::where('name', 'YouTube')->first();
        $socialProfile = SocialProfile::create([
            'influencer_id' => $moure->id,
            'platform_id' => $platform->id,
            'username' => 'mouredev',
            'profile_url' => "https://www.youtube.com/@mouredev",
        ]);
        $platform = Platform::where('name', 'Twitter')->first();
        $socialProfile = SocialProfile::create([
            'influencer_id' => $moure->id,
            'platform_id' => $platform->id,
            'username' => 'MoureDev',
            'profile_url' => "https://twitter.com/MoureDev",
        ]);
        $ibai = Influencer::where('name', 'LIKE', '%ibaillanos%')->first();
        $platform = Platform::where('name', 'YouTube')->first();
        $socialProfile = SocialProfile::create([
            'influencer_id' => $ibai->id,
            'platform_id' => $platform->id,
            'username' => 'IbaiLlanos',
            'profile_url' => "https://www.youtube.com/@IbaiLlanos",
        ]);
        $platform = Platform::where('name', 'Twitter')->first();
        $socialProfile = SocialProfile::firstOrCreate([
            'influencer_id' => $ibai->id,
            'platform_id' => $platform->id,
            'username' => 'IbaiLlanos',
            'profile_url' => "https://x.com/IbaiLlanos",
        ]);
    
    
    }
    /**
     * Parse the followers count from the response.
     *
     * @param string $followersCount
     * @return int
     */
    public function dataFromAPIInstagram($username)
    {
        $url = 'https://i.instagram.com/api/v1/users/web_profile_info/?username=' . $username;

        // Configurar los encabezados
        $headers = [
            "User-Agent: iphone_ua",
            "x-ig-app-id: 936619743392459"
        ];

        // Inicializar cURL
        $ch = curl_init();

        // Establecer la URL y los encabezados
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Para obtener la respuesta

        // Ejecutar la solicitud
        $response = curl_exec($ch);
        // Verificar si hubo un error
        if ($response === false) {
            return response()->json(['error' => 'Error en la solicitud cURL: ' . curl_error($ch)], 500);
        }

        // Cerrar la sesión cURL
        curl_close($ch);
        // Decodificar la respuesta JSON
        $response = json_decode($response);
        return $response;
    }
}
