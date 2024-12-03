<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPreference>
 */
class UserPreferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserPreference::class;

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

        $authors = [
            'David',
            'Liam',
            'Arthor'
        ];

        return [
            'user_id' => User::factory(),
            'preferred_sources' => $this->faker->randomElements($sources, 3),
            'preferred_categories' => $this->faker->randomElements($categories, 2),
            'preferred_authors' => $this->faker->randomElements($authors, 2),
        ];
    }
}
