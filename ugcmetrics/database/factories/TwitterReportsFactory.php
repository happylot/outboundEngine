<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TwitterReportsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'year' => $this->faker->year,
            'month' => $this->faker->month,
            'followers_start' => $this->faker->numberBetween(1000, 100000),
            'followers_end' => $this->faker->numberBetween(1000, 100000),
            'growth_rate' => $this->faker->randomFloat(2, -5, 5),
            'tweets_count' => $this->faker->numberBetween(10, 100),
            'likes_count' => $this->faker->numberBetween(1000, 50000),
            'comments_count' => $this->faker->numberBetween(100, 10000),
            'retweets_count' => $this->faker->numberBetween(100, 20000),
            'avg_engagement_rate' => $this->faker->randomFloat(2, 0, 10),
            'impressions' => $this->faker->numberBetween(10000, 1000000),
            'profile_visits' => $this->faker->numberBetween(1000, 50000),
            'mentions_count' => $this->faker->numberBetween(10, 1000),
            'hashtag_performance' => json_encode([
                '#trending' => $this->faker->numberBetween(100, 1000),
                '#viral' => $this->faker->numberBetween(100, 1000)
            ]),
        ];
    }
}