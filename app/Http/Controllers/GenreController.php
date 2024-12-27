<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenreRequest;
use App\Models\Genre;
use Illuminate\Support\Facades\Cache;

class GenreController extends Controller
{
    /**
     * List all genres with caching.
     */
    public function index()
    {
        $genres = Cache::remember('genres', now()->addMinutes(5), function () {
            return Genre::all();
        });

        return response()->json($genres);
    }

    /**
     * Create a new genre.
     */
    public function store(GenreRequest $request)
    {
        $data = $request->validated();
        
        $genre = Genre::factory()->withName($data['name']);

        Cache::forget('genres');

        $genre = $genre->create();
        return response()->json($genre, 201);
    }

    /**
     * Show details of a specific genre.
     */
    public function show(Genre $genre)
    {
        return response()->json($genre);
    }

    /**
     * Update a genre.
     */
    public function update(GenreRequest $request, Genre $genre)
    {
        $validated = $request->validated();

        $genre->update($validated);

        // Limpiar cache
        Cache::forget('genres');

        return response()->json($genre);
    }

    /**
     * Delete a genre.
     */
    public function destroy(Genre $genre)
    {
        $genre->delete();

        // Limpiar cache
        Cache::forget('genres');

        return response()->json(null, 204);
    }
}
