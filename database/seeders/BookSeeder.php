<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Author;
use App\Models\Genre;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authorIds = Author::pluck('id')->toArray();
        $genreIds = Genre::pluck('id')->toArray();

        Book::factory(10)->fake()->state(function ($attr) use ($authorIds, $genreIds) {
            return [
                'author_id' => $authorIds[array_rand($authorIds)],
                'genre_id' => $genreIds[array_rand($genreIds)],
            ];
        })->create();
    }
}
