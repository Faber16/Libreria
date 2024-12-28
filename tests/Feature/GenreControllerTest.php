<?php

namespace Tests\Feature;

use App\Models\Genre;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the index method with caching.
     */
    public function test_index_returns_cached_genres()
    {
        Genre::factory()->fake()->count(5)->create();

        Cache::shouldReceive('remember')
            ->once()
            ->with('genres', \Mockery::type('Illuminate\Support\Carbon'), \Mockery::type('Closure'))
            ->andReturn(Genre::all());

        $response = $this->getJson('/api/genres');

        $response->assertStatus(200)
                 ->assertJsonCount(5);
    }

    /**
     * Test the store method to create a genre.
     */
    public function test_store_creates_genre_and_clears_cache()
    {
        Cache::shouldReceive('forget')->once()->with('genres');

        $data = [
            'name' => 'Fantasy',
        ];

        $response = $this->postJson('/api/genres', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'name' => 'Fantasy',
                     'slug' => 'fantasy',
                 ]);

        $this->assertDatabaseHas('genres', $data);
    }

    /**
     * Test the show method to retrieve a genre's details.
     */
    public function test_show_returns_genre_details()
    {
        $genre = Genre::factory()->fake()->create();

        $response = $this->getJson("/api/genres/{$genre->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $genre->id,
                     'name' => $genre->name,
                     'slug' => $genre->slug,
                 ]);
    }

    /**
     * Test the update method to modify a genre.
     */
    public function test_update_modifies_genre_and_clears_cache()
    {
        $genre = Genre::factory()->fake()->create();

        Cache::shouldReceive('forget')->once()->with('genres');

        $data = [
            'name' => 'Science Fiction',
        ];

        $response = $this->putJson("/api/genres/{$genre->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'name' => 'Science Fiction',
                     'slug' => 'science-fiction',
                 ]);

        $this->assertDatabaseHas('genres', $data);
    }

    /**
     * Test the destroy method to delete a genre.
     */
    public function test_destroy_deletes_genre_and_clears_cache()
    {
        $genre = Genre::factory()->fake()->create();

        Cache::shouldReceive('forget')->once()->with('genres');

        $response = $this->deleteJson("/api/genres/{$genre->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('genres', ['id' => $genre->id]);
    }
}
