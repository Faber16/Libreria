<?php

namespace Database\Factories;

use Cocur\Slugify\Bridge\Laravel\SlugifyFacade;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Genre>
 */
class GenreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_at' => now(),
            'updated_at' => null,
            'deleted_at' => null,
        ];
    }

    public function fake(): GenreFactory {
        return $this->state(function (array $attrs) {
            $name = fake()->word();
            return [
                'name' => $name,
                'slug' => SlugifyFacade::slugify($name),
            ];
        });
    }

    public function withName(string $name): GenreFactory {
        return $this->state(function (array $attrs) use ($name) {
            return [
                'name' => $name,
                'slug' => SlugifyFacade::slugify($name),
            ];
        });
    }
}
