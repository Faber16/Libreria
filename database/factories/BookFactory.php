<?php

namespace Database\Factories;


use Cocur\Slugify\Bridge\Laravel\SlugifyFacade;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Type\Integer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return[
            'created_at' => now(),
            'updated_at' => null,
            'deleted_at' => null,
            'picture' => null,
        ];
    }


    public function fake(): BookFactory {
        return $this->state(function (array $attrs) {
            $name = fake()->sentence();
            $year = fake()->numberBetween(1900, 2024);
            return [
                'name' => $name,
                'slug' => SlugifyFacade::slugify($name),
                'year_publication' => $year,
            ];
        });
    }


    public function withName(string $name): BookFactory {
        return $this->state(function (array $attrs) use ($name) {
            return [
                'name' => $name,
                'slug' => SlugifyFacade::slugify($name),
            ];
        });
    }

    public function withYear(int $year): BookFactory {
        return $this->state(function (array $attrs) use ($year) {
            return [
                'year_publication' => $year,
            ];
        });
    }

    
}
