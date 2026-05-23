<?php

namespace Database\Seeders;

use App\Models\Influencer;
use App\Models\Platform;
use App\Models\SocialProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TwitterInfluencersSeeder extends Seeder
{
    public function run(): void
    {
        $platform = Platform::firstOrCreate(
            ['name' => 'Twitter'],
            [
                'description' => 'Twitter social media platform',
                'base_url' => 'https://twitter.com/'
            ]
        );

        $twitterInfluencers = [
            'elonmusk', 'BillGates', 'neiltyson', 'BarackObama', 
            'tim_cook', 'jack', 'PMOIndia', 'narendramodi',
            'katyperry', 'rihanna', 'taylorswift13', 'Cristiano'
        ];

        foreach ($twitterInfluencers as $username) {
            $influencer = Influencer::firstOrCreate([
                'username' => $username
            ], [
                'name' => $username,
                'bio' => '',
                'profile_picture_url' => ''
            ]);

            SocialProfile::firstOrCreate([
                'influencer_id' => $influencer->id,
                'platform_id' => $platform->id,
                'username' => $username
            ], [
                'profile_url' => "https://twitter.com/{$username}",
                'profile_picture' => ''
            ]);
        }
    }
}