<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

 /**
 * @OA\Schema(
 *  schema="AuthorRequest",
 *  title="AuthorRequest",
 * 	@OA\Property(
 * 		property="full_name",
 * 		type="string"
 * 	),
 * 	@OA\Property(
 * 		property="alias",
 * 		type="string"
 * 	)
 * )
 */
class AuthorRequest extends FormRequest
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
            'full_name' => 'required|string|max:255',
            'alias' => 'string|max:255',
        ];
    }
}
