<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_cached_books()
    {
        $author = Author::factory()->fake()->create();
        $genre = Genre::factory()->fake()->create();

        Book::factory()->fake()->count(5)->create([
            'author_id' => $author->id,
            'genre_id' => $genre->id,
        ]);

        Cache::shouldReceive('remember')
            ->once()
            ->with('books', \Mockery::type('Illuminate\Support\Carbon'), \Mockery::type('Closure'))
            ->andReturn(Book::with(['author', 'genre'])->get());

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
                 ->assertJsonCount(5);
    }

    public function test_store_creates_book_and_clears_cache()
    {
        $author = Author::factory()->fake()->create();
        $genre = Genre::factory()->fake()->create();

        Cache::shouldReceive('forget')->once()->with('books');

        $data = [
            'name' => 'The Great Gatsby',
            'author_id' => $author->id,
            'genre_id' => $genre->id,
            'year_publication' => 1925,
        ];

        $response = $this->postJson('/api/books', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'name' => 'The Great Gatsby',
                     'slug' => 'the-great-gatsby',
                     'author_id' => $author->id,
                     'genre_id' => $genre->id,
                     'year_publication' => 1925,
                 ]);

        $this->assertDatabaseHas('books', $data);
    }

    public function test_show_returns_book_details()
    {
        $author = Author::factory()->fake()->create();
        $genre = Genre::factory()->fake()->create();

        $book = Book::factory()->fake()->create([
            'author_id' => $author->id,
            'genre_id' => $genre->id,
        ]);

        $response = $this->getJson("/api/books/{$book->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $book->id,
                     'name' => $book->name,
                     'author_id' => $book->author_id,
                     'genre_id' => $book->genre_id,
                 ]);
    }

    public function test_update_modifies_book_and_clears_cache()
    {
        $author = Author::factory()->fake()->create();
        $genre = Genre::factory()->fake()->create();

        $book = Book::factory()->fake()->create([
            'author_id' => $author->id,
            'genre_id' => $genre->id,
        ]);

        Cache::shouldReceive('forget')->once()->with('books');

        $data = [
            'name' => 'Updated Book Name',
            'author_id' => $author->id,
            'genre_id' => $genre->id,
            'year_publication' => 2022,
            'picture' => 'https://example.com/updated-picture.jpg',
        ];

        $response = $this->putJson("/api/books/{$book->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'name' => 'Updated Book Name',
                     'slug' => 'updated-book-name',
                     'year_publication' => 2022,
                 ]);

        $this->assertDatabaseHas('books', $data);
    }

    public function test_destroy_deletes_book_and_clears_cache()
    {
        $author = Author::factory()->fake()->create();
        $genre = Genre::factory()->fake()->create();

        $book = Book::factory()->fake()->create([
            'author_id' => $author->id,
            'genre_id' => $genre->id,
        ]);

        Cache::shouldReceive('forget')->once()->with('books');

        $response = $this->deleteJson("/api/books/{$book->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('books', ['id' => $book->id]);
    }
}
