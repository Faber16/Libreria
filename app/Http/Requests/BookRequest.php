<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *  schema="BookRequest",
 *  title="BookRequest",
 *  @OA\Property(
 *      property="name",
 *      type="string",
 *      description="Name of the book",
 *      example="The Great Gatsby"
 *  ),
 *  @OA\Property(
 *      property="author_id",
 *      type="number",
 *      description="ID of the author",
 *      example=1
 *  ),
 *  @OA\Property(
 *      property="genre_id",
 *      type="number",
 *      description="ID of the genre",
 *      example=2
 *  ),
 *  @OA\Property(
 *      property="year_publication",
 *      type="number",
 *      description="Year of publication",
 *      example=1925
 *  )
 * )
 */

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'genre_id' => 'required|exists:genres,id',
            'year_publication' => 'required|integer',
            'picture' => 'nullable|url',
        ];
    }
}
