<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Models\Author;
use Illuminate\Support\Facades\Cache;

class AuthorController extends Controller
{
    /**
     * List all authors with caching.
     */
    public function index()
    {
        $authors = Cache::remember('authors', now()->addMinutes(5), function () {
            return Author::all();
        });

        return response()->json($authors);
    }

    /**
     * Create a new author.
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
     */
    public function show(Author $author)
    {
        return response()->json($author);
    }

    /**
     * Update an author.
     */
    public function update(AuthorRequest $request, Author $author)
    {
        $author->update($request->validated());

        Cache::forget('authors');

        return response()->json($author);
    }

    /**
     * Delete an author.
     */
    public function destroy(Author $author)
    {
        $author->delete();

        Cache::forget('authors');

        return response()->json(null, 204);
    }
}
