<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Author;
use App\Models\Genre;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BooksPictureControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_image_uploads_and_updates_picture_field()
    {
        Storage::fake('s3');

        $author = Author::factory()->fake()->create();
        $genre = Genre::factory()->fake()->create();
        $book = Book::factory()->fake()->create([
            'author_id' => $author->id,
            'genre_id' => $genre->id,
        ]);

        $imageContent = Http::get('https://picsum.photos/seed/picsum/200/300')->body();
        $imagePath = tempnam(sys_get_temp_dir(), 'test_image');
        file_put_contents($imagePath, $imageContent);

        $image = new UploadedFile(
            $imagePath,
            'test.jpg',
            'image/jpeg',
            null,
            true
        );

        $response = $this->post("/api/books/{$book->id}/picture", [
            'image' => $image,
        ]);

        $response->assertStatus(204);

        $book->fresh();

        Storage::disk('s3')->assertExists($book->picture);
    }

    public function test_store_image_fails_with_invalid_image()
    {
        $author = Author::factory()->fake()->create();
        $genre = Genre::factory()->fake()->create();
        $book = Book::factory()->fake()->create([
            'author_id' => $author->id,
            'genre_id' => $genre->id,
        ]);

        $response = $this->post("/api/books/{$book->id}/picture", [
            'image' => 'not-a-valid-file',
        ]);

        $response->assertStatus(302); 

        $response = $this->postJson("/api/books/{$book->id}/picture", [
            'image' => 'not-a-valid-file',
        ]);

        $response->assertStatus(422); 
    }

    public function test_get_book_image_returns_image_stream()
    {
        Storage::fake('s3');


        $author = Author::factory()->fake()->create();
        $genre = Genre::factory()->fake()->create();
        $book = Book::factory()->fake()->create([
            'picture' => 'images/test.jpg',
            'author_id' => $author->id,
            'genre_id' => $genre->id,
        ]);

        $imageContent = Http::get('https://picsum.photos/seed/picsum/200/300')->body();
        Storage::disk('s3')->put('images/test.jpg', $imageContent);

        $response = $this->get("/api/books/{$book->id}/picture");

        $response->assertStatus(200)
                 ->assertHeader('Content-Type', 'image/jpeg')
                 ->assertHeader('Content-Disposition', 'inline; filename="test.jpg"');
    }

    public function test_get_book_image_returns_404_when_image_does_not_exist()
    {
        Storage::fake('s3');

        $author = Author::factory()->fake()->create();
        $genre = Genre::factory()->fake()->create();
        $book = Book::factory()->fake()->create([
            'picture' => 'images/nonexistent.jpg',
            'author_id' => $author->id,
            'genre_id' => $genre->id,
        ]);

        $response = $this->get("/api/books/{$book->id}/picture");

        $response->assertStatus(404);
    }

    public function test_get_book_image_returns_404_when_book_does_not_exist()
    {
        $response = $this->get('/api/books/9999/picture');

        $response->assertStatus(404);
    }
}
