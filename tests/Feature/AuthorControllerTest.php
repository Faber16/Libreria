<?php

namespace Tests\Feature;

use App\Models\Author;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_cached_authors()
    {
        Author::factory()->fake()->count(5)->create();

        Cache::shouldReceive('remember')
            ->once()
            ->with('authors', \Mockery::type('Illuminate\Support\Carbon'), \Mockery::type('Closure'))
            ->andReturn(Author::all());

        $response = $this->getJson('/api/authors');

        $response->assertStatus(200)
                 ->assertJsonCount(5);
    }

    public function test_store_creates_author_and_clears_cache()
    {
        Cache::shouldReceive('forget')->once()->with('authors');

        $data = [
            'full_name' => 'Jane Doe',
            'alias' => 'jdoe',
        ];

        $response = $this->postJson('/api/authors', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'full_name' => 'Jane Doe',
                     'alias' => 'jdoe',
                     'initials' => 'J.D.',
                 ]);

        $this->assertDatabaseHas('authors', $data);
    }

    public function test_show_returns_author_details()
    {
        $author = Author::factory()->fake()->create();

        $response = $this->getJson("/api/authors/{$author->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $author->id,
                     'full_name' => $author->full_name,
                     'alias' => $author->alias,
                 ]);
    }

    public function test_update_modifies_author_and_clears_cache()
    {
        $author = Author::factory()->fake()->create();

        Cache::shouldReceive('forget')->once()->with('authors');

        $data = [
            'full_name' => 'John Smith',
            'alias' => 'jsmith',
        ];

        $response = $this->putJson("/api/authors/{$author->id}", $data);

        $response->assertStatus(200)
                 ->assertJson($data);

        $this->assertDatabaseHas('authors', $data);
    }

    public function test_destroy_deletes_author_and_clears_cache()
    {
        $author = Author::factory()->fake()->create();

        Cache::shouldReceive('forget')->once()->with('authors');

        $response = $this->deleteJson("/api/authors/{$author->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('authors', ['id' => $author->id]);
    }

}
