<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    /**
     * List all books with caching.
     * 
     * @OA\Get(
     *     path="/api/books",
     *     summary="Get all books",
     *     description="Retrieve a list of all books, including their authors and genres, cached for 5 minutes.",
     *     tags={"Books"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of books",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Book")
     *         )
     *     )
     * )
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
     * 
     * @OA\Post(
     *     path="/api/books",
     *     summary="Create a new book",
     *     description="Add a new book to the database.",
     *     tags={"Books"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BookRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Book created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Book")
     *     )
     * )
     */
    public function store(BookRequest $request)
    {
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
     * 
     * @OA\Get(
     *     path="/api/books/{id}",
     *     summary="Get book details",
     *     description="Retrieve the details of a specific book by ID, including its author and genre.",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the book to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book details",
     *         @OA\JsonContent(ref="#/components/schemas/Book")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book not found"
     *     )
     * )
     */
    public function show(Book $book)
    {
        return response()->json($book->load(['author', 'genre']));
    }

    /**
     * Update a book.
     * 
     * @OA\Put(
     *     path="/api/books/{id}",
     *     summary="Update a book",
     *     description="Update the details of an existing book by ID.",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the book to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BookRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Book")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book not found"
     *     )
     * )
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
     * 
     * @OA\Delete(
     *     path="/api/books/{id}",
     *     summary="Delete a book",
     *     description="Soft delete a specific book by ID.",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the book to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Book deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book not found"
     *     )
     * )
     */
    public function destroy(Book $book)
    {
        $book->delete();

        Cache::forget('books');

        return response()->json(null, 204);
    }
}
