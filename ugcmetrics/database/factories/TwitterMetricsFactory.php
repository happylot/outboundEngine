<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TwitterMetricsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'date' => $this->faker->date(),
            'followers' => $this->faker->numberBetween(1000, 1000000),
            'following' => $this->faker->numberBetween(100, 5000),
            'tweets' => $this->faker->numberBetween(100, 10000),
            'listed' => $this->faker->numberBetween(0, 500),
            'likes' => $this->faker->numberBetween(0, 50000),
            'comments' => $this->faker->numberBetween(0, 10000),
            'retweets' => $this->faker->numberBetween(0, 20000),
            'engagement_rate' => $this->faker->randomFloat(2, 0, 10),
            'impressions' => $this->faker->numberBetween(1000, 1000000),
            'profile_visits' => $this->faker->numberBetween(100, 50000),
            'mentions' => $this->faker->numberBetween(0, 1000),
        ];
    }
}