<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    /**
     * List all books with caching.
     */
    public function index()
    {
        $books = Cache::remember('books', now()->addMinutes(5), function () {
            return Book::with(['author', 'genre'])->get();
        });

        return response()->json($books);
    }

    /**
     * Create a new book.
     */
    public function store(BookRequest $request)
    {
        // Generar datos del libro usando el factory
        $bookData = Book::factory()
            ->withName($request->input('name'))
            ->withYear($request->input('year_publication'))
            ->state([
                'picture' => $request->input('picture'),
                'author_id' => $request->input('author_id'),
                'genre_id' => $request->input('genre_id'),
            ])
            ->make()
            ->toArray();

        $book = Book::create($bookData);

        Cache::forget('books');

        return response()->json($book, 201);
    }

    /**
     * Show details of a specific book.
     */
    public function show(Book $book)
    {
        return response()->json($book->load(['author', 'genre']));
    }

    /**
     * Update a book.
     */
    public function update(BookRequest $request, Book $book)
    {
        $validated = $request->validated();

        $book->update($validated);

        Cache::forget('books');

        return response()->json($book);
    }

    /**
     * Delete a book (soft delete).
     */
    public function destroy(Book $book)
    {
        $book->delete();

        // Limpiar cache
        Cache::forget('books');

        return response()->json(null, 204);
    }

}
