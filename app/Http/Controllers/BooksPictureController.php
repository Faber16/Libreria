<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Book;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Cache;


class BooksPictureController extends Controller
{
    /**
     * Upload an image for a book and update the book's picture field.
     * 
     * @OA\Post(
     *     path="/api/books/{id}/picture",
     *     summary="Upload a book's image",
     *     description="Uploads an image for the specified book and updates its picture field with the image path.",
     *     tags={"Books", "Pictures"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the book",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"image"},
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="The image file to upload"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Image uploaded successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book not found"
     *     )
     * )
     */
    public function storeImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $book = Book::findOrFail($id);

        $image = $request->file('image');

        $randomName = Str::uuid() . '.' . $image->getClientOriginalExtension();

        $path = $image->storeAs('images', $randomName, 's3');

        $book->update(['picture' => $path]);

        return response()->noContent();
    }

    /**
     * Get a book's image from storage.
     * 
     * @OA\Get(
     *     path="/api/books/{id}/picture",
     *     summary="Get a book's image",
     *     description="Retrieves the image associated with the specified book and returns it as a binary stream.",
     *     tags={"Books", "Pictures"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the book",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image retrieved successfully",
     *         @OA\MediaType(
     *             mediaType="image/jpeg"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image not found"
     *     )
     * )
     */
    public function getBookImage($id)
    {
        $picturePath = Cache::remember("book_picture_{$id}", now()->addMinutes(5), function () use ($id) {
            $book = Book::findOrFail($id);
            return $book->picture;
        });
    
        if (!$picturePath || !Storage::disk('s3')->exists($picturePath)) {
            abort(404, 'Imagen no encontrada');
        }
    
        $imageStream = Storage::disk('s3')->readStream($picturePath);
    
        return new StreamedResponse(function () use ($imageStream) {
            fpassthru($imageStream);
        }, 200, [
            'Content-Type' => Storage::disk('s3')->mimeType($picturePath),
            'Content-Disposition' => 'inline; filename="' . basename($picturePath) . '"',
        ]);
    }
}
