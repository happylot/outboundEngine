<?php

namespace Database\Seeders;

use App\Models\TwitterReports;
use Illuminate\Database\Seeder;

class TwitterReportsSeeder extends Seeder
{
    public function run(): void
    {
        TwitterReports::factory()
            ->count(12) // 1 year of monthly reports
            ->create();
    }
}