<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Book;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;


class BooksPictureController extends Controller
{
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


    public function getBookImage($id)
    {
        $book = Book::findOrFail($id);

        if (!$book->picture || !Storage::disk('s3')->exists($book->picture)) {
            abort(404, 'Imagen no encontrada');
        }

        $imageStream = Storage::disk('s3')->readStream($book->picture);

        return new StreamedResponse(function () use ($imageStream) {
            fpassthru($imageStream);
        }, 200, [
            'Content-Type' => Storage::disk('s3')->mimeType($book->picture),
            'Content-Disposition' => 'inline; filename="' . basename($book->picture) . '"',
        ]);
    }

}
