<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private $seeders = [
        AuthorSeeder::class,
        GenreSeeder::class,
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->seeders as $seeder) {
            $instance = new $seeder();
            $instance->run();
        }
    }
}
