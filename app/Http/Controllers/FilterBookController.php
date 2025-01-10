<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class FilterBookController extends Controller
{
    /**
     * Muestra la información de un libro específico, además
     * de sugerir otros 3 libros del mismo género.
     */
    public function show($id)
    {
        $book = Book::findOrFail($id);

        $suggestedBooks = Book::where('genre_id', $book->genre_id)
            ->where('id', '!=', $book->id)
            ->inRandomOrder() 
            ->take(3)
            ->get();

        return response()->json([
            'book' => $book,
            'suggested_books' => $suggestedBooks
        ]);
    }
}
