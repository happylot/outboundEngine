<?php

namespace Database\Seeders;

use App\Models\TwitterMetrics;
use Illuminate\Database\Seeder;

class TwitterMetricsSeeder extends Seeder
{
    public function run(): void
    {
        TwitterMetrics::factory()
            ->count(50)
            ->create();
    }
}