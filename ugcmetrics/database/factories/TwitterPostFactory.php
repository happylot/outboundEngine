<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TwitterPostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'post_id' => $this->faker->unique()->uuid,
            'content' => $this->faker->sentence(10),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'likes' => $this->faker->numberBetween(0, 50000),
            'comments' => $this->faker->numberBetween(0, 10000),
            'retweets' => $this->faker->numberBetween(0, 20000),
            'views' => $this->faker->numberBetween(1000, 1000000),
            'engagement_rate' => $this->faker->randomFloat(2, 0, 10),
            'media_urls' => json_encode([$this->faker->imageUrl()]),
            'hashtags' => json_encode(['#'.$this->faker->word, '#'.$this->faker->word]),
            'mentions' => json_encode(['@'.$this->faker->userName, '@'.$this->faker->userName]),
            'is_retweet' => $this->faker->boolean(20),
            'is_reply' => $this->faker->boolean(30),
            'language' => $this->faker->languageCode,
        ];
    }
}