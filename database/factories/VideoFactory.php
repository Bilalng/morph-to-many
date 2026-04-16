<?php

namespace Database\Factories;

use App\Models\Video;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Video>
 */
class VideoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'title'       => fake()->sentence(),
            'description' => fake()->paragraph(),
            'url'         => fake()->url(),
        ];
    }
}
