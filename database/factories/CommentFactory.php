<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => fake()->sentence(),
            // Alt kısımları testte biz kendimiz ezeceğiz ama boş kalmasın diye varsayılan değer veriyoruz
            'commentable_type' => 'post', 
            'commentable_id' => 1,
        ];
    }
}
