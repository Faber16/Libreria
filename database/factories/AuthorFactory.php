<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author>
 */
class AuthorFactory extends Factory
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
            'alias'      => null,
        ];
    }

    private function get_initial(string $text): string {
        if (strlen($text) == 0) {
            return "";
        }

        return strtoupper($text[0]);
    }

    public function fake(): AuthorFactory {
        return $this->state(function (array $attrs) {
            $name = fake()->name();
            $initals = implode(".", array_map([$this, 'get_initial'], explode(' ', $name))) . ".";
            $alias = fake()->name();
            return [
                'full_name' => $name,
                'initials' => $initals,
                'alias' => $alias,
            ];
        });
    }

    public function withName(string $name): AuthorFactory {
        return $this->state(function (array $attrs) use ($name) {
            $initals = implode(". ", array_map([$this, 'get_initial'], explode(' ', $name))) . ".";
            return [
                'full_name' => $name,
                'initials' => $initals,
            ];
        });
    }

    public function withAlias(string $alias): AuthorFactory {
        return $this->state(function (array $attrs) use ($alias) {
            return [
                'alias' => $alias,
            ];
        });
    }
}
