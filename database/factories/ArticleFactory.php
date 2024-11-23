<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['World', 'Technology', 'Health', 'Business', 'Sports', 'Entertainment', 'Science'];
        $sources = [
            'NewsAPI',
            'OpenNews',
            'NewsCred',
            'The Guardian',
            'New York Times',
            'BBC News',
            'NewsAPI.org',
        ];

        return [
            'title' => $this->faker->sentence(mt_rand(6, 12)),
            'content' => $this->faker->paragraphs(mt_rand(3, 7), true),
            'author' => $this->faker->name,
            'category' => $this->faker->randomElement($categories),
            'source' => $this->faker->randomElement($sources),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
