<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenreRequest;
use App\Models\Genre;
use Illuminate\Support\Facades\Cache;

class GenreController extends Controller
{
    /**
     * List all genres with caching.
     * 
     * @OA\Get(
     *     path="/api/genres",
     *     summary="Get all genres",
     *     description="Retrieve a list of all genres, cached for 5 minutes.",
     *     tags={"Genres"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of genres",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Genre")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $genres = Cache::remember('genres', now()->addMinutes(5), function () {
            return Genre::orderBy('created_at', 'desc')->get();
        });

        return response()->json($genres);
    }

    /**
     * Create a new genre.
     * 
     * @OA\Post(
     *     path="/api/genres",
     *     summary="Create a new genre",
     *     description="Add a new genre to the database.",
     *     tags={"Genres"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/GenreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Genre created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Genre")
     *     )
     * )
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
     * 
     * @OA\Get(
     *     path="/api/genres/{id}",
     *     summary="Get genre details",
     *     description="Retrieve the details of a specific genre by ID.",
     *     tags={"Genres"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the genre to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Genre details",
     *         @OA\JsonContent(ref="#/components/schemas/Genre")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Genre not found"
     *     )
     * )
     */
    public function show(Genre $genre)
    {
        return response()->json($genre);
    }

    /**
     * Update a genre.
     * 
     * @OA\Put(
     *     path="/api/genres/{id}",
     *     summary="Update a genre",
     *     description="Update the details of an existing genre by ID.",
     *     tags={"Genres"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the genre to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/GenreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Genre updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Genre")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Genre not found"
     *     )
     * )
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
     * 
     * @OA\Delete(
     *     path="/api/genres/{id}",
     *     summary="Delete a genre",
     *     description="Delete a specific genre by ID.",
     *     tags={"Genres"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the genre to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Genre deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Genre not found"
     *     )
     * )
     */
    public function destroy(Genre $genre)
    {
        $genre->delete();

        // Limpiar cache
        Cache::forget('genres');

        return response()->json(null, 204);
    }
}
