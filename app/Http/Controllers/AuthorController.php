<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Models\Author;
use Illuminate\Support\Facades\Cache;

class AuthorController extends Controller
{
    /**
     * List all authors with caching.
     * 
     * @OA\Get(
     *     path="/api/authors",
     *     summary="Get all authors",
     *     description="Retrieve a list of all authors, cached for 5 minutes.",
     *     tags={"Authors"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of authors",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Author")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $authors = Cache::remember('authors', now()->addMinutes(5), function () {
            return Author::orderBy('created_at', 'desc')->get();
        });

        return response()->json($authors);
    }

    /**
     * Create a new author.
     * 
     * @OA\Post(
     *     path="/api/authors",
     *     summary="Create a new author",
     *     description="Add a new author to the database.",
     *     tags={"Authors"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AuthorRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Author created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Author")
     *     )
     * )
     */
    public function store(AuthorRequest $request)
    {
        $data = $request->validated();

        $author = Author::factory()->withName($data['full_name']);
        if (isset($data['alias']) && !is_null($data['alias'])) {
            $author = $author->withAlias($data['alias']);
        }

        Cache::forget('authors');

        $author = $author->create();
        return response()->json($author, 201);
    }

    /**
     * Show details of a specific author.
     * 
     * @OA\Get(
     *     path="/api/authors/{id}",
     *     summary="Get author details",
     *     description="Retrieve the details of a specific author by ID.",
     *     tags={"Authors"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the author to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Author details",
     *         @OA\JsonContent(ref="#/components/schemas/Author")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Author not found"
     *     )
     * )
     */
    public function show(Author $author)
    {
        return response()->json($author);
    }

    /**
     * Update an author.
     * 
     * @OA\Put(
     *     path="/api/authors/{id}",
     *     summary="Update an author",
     *     description="Update the details of an existing author by ID.",
     *     tags={"Authors"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the author to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AuthorRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Author updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Author")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Author not found"
     *     )
     * )
     */
    public function update(AuthorRequest $request, Author $author)
    {
        $author->update($request->validated());

        Cache::forget('authors');

        return response()->json($author);
    }

    /**
     * Delete an author.
     * 
     * @OA\Delete(
     *     path="/api/authors/{id}",
     *     summary="Delete an author",
     *     description="Delete a specific author by ID.",
     *     tags={"Authors"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the author to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Author deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Author not found"
     *     )
     * )
     */
    public function destroy(Author $author)
    {
        $author->delete();

        Cache::forget('authors');

        return response()->json(null, 204);
    }
}
