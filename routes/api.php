<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BooksPictureController;
use App\Http\Controllers\FilterBookController;





/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::apiResource('authors', AuthorController::class);
Route::apiResource('genres', GenreController::class);
Route::apiResource('books', BookController::class);

Route::post('books/{id}/picture', [BooksPictureController::class, 'storeImage']);
Route::get('books/{id}/picture', [BooksPictureController::class, 'getBookImage']);

Route::get('/books-filter/{id}', [FilterBookController::class, 'show']);

