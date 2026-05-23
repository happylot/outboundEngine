<?php

namespace Database\Seeders;

use App\Models\TwitterPost;
use Illuminate\Database\Seeder;

class TwitterPostSeeder extends Seeder
{
    public function run(): void
    {
        TwitterPost::factory()
            ->count(100)
            ->create();
    }
}