<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        $title = $this->faker->sentence;

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'content' => $this->faker->paragraphs(3, true),
            'url_slug' => Str::slug($title) . '-' . Str::random(6),
            'meta_description' => $this->faker->sentence,
            'deleted_at' => null,
        ];
    }
}
