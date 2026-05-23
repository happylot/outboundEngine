<?php

namespace Database\Seeders;

use App\Models\SeedingDevice;
use App\Models\SeedingBehaviorGroup;
use App\Models\SeederAccount;
use Illuminate\Database\Seeder;

class SeedingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Devices
        $device1 = SeedingDevice::create([
            'device_name' => 'iPhone 11 Pro - 01',
            'device_model' => 'A2111',
            'os_version' => 'iOS 15.4',
            'connection_type' => 'Residential Proxy',
            'proxy_info' => '45.138.22.10:8000:user1:pass1',
            'status' => 'active',
            'notes' => 'Primary Meta seeding device.',
        ]);

        $device2 = SeedingDevice::create([
            'device_name' => 'Samsung S20 - 01',
            'device_model' => 'SM-G981B',
            'os_version' => 'Android 11',
            'connection_type' => '4G LTE (Cellular)',
            'status' => 'active',
            'notes' => 'Mobile 4G backup device.',
        ]);

        // 2. Create Behavior Groups
        $group1 = SeedingBehaviorGroup::create([
            'name' => 'Nhóm 1 (Tương tác nhẹ)',
            'daily_limit' => 5,
            'min_interval_minutes' => 5, // 5m for fast testing
            'max_interval_minutes' => 10,
            'description' => 'Light behavior testing. Minimum 5 minutes cooldown, maximum 5 actions per day.',
        ]);

        $group2 = SeedingBehaviorGroup::create([
            'name' => 'Nhóm 3 (Stress Test)',
            'daily_limit' => 20,
            'min_interval_minutes' => 1,
            'max_interval_minutes' => 2,
            'description' => 'High intensity behavior. Fast intervals to test checkpoint sensitivity.',
        ]);

        // 3. Create Seeder Accounts
        SeederAccount::create([
            'username' => 'nguyenvana_seeder',
            'platform' => 'facebook',
            'profile_url' => 'https://facebook.com/nguyenvana_seeder',
            'age_group' => '1-3 years',
            'device_id' => $device1->id,
            'behavior_group_id' => $group1->id,
            'status' => 'active',
            'notes' => 'Facebook aged test account',
        ]);

        SeederAccount::create([
            'username' => 'lethib_insta',
            'platform' => 'instagram',
            'profile_url' => 'https://instagram.com/lethib_insta',
            'age_group' => '6-12 months',
            'device_id' => $device1->id,
            'behavior_group_id' => $group1->id,
            'status' => 'active',
            'notes' => 'Instagram account',
        ]);

        SeederAccount::create([
            'username' => 'reddit_tester_99',
            'platform' => 'reddit',
            'profile_url' => 'https://reddit.com/user/reddit_tester_99',
            'age_group' => '< 6 months',
            'device_id' => $device2->id,
            'behavior_group_id' => $group2->id,
            'status' => 'active',
            'notes' => 'Reddit new account for stress testing',
        ]);

        SeederAccount::create([
            'username' => 'tiktok_seeder_vn',
            'platform' => 'tiktok',
            'profile_url' => 'https://tiktok.com/@tiktok_seeder_vn',
            'age_group' => '> 3 years',
            'device_id' => $device2->id,
            'behavior_group_id' => $group2->id,
            'status' => 'active',
            'notes' => 'Aged TikTok account',
        ]);
    }
}
