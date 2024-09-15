<?php

namespace App\Http\Requests;



/**
 * @OA\Schema(
 *     schema="StoreMediaRequest",
 *     type="object",
 *     title="Media  Store Request",
 *     description="Media  store request data",
 *     required={"image","name","caption","description"},
 *     @OA\Property(
 *         property="image",
 *         type="array",
 *         @OA\Items(type="string", format="binary"),
 *         description="Array of the image",
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="array",
 *         @OA\Items(type="string"),
 *         description="Name of the image",
 *     ),
 *     @OA\Property(
 *         property="caption",
 *         type="array",
 *         @OA\Items(type="string"),
 *         description="Caption of the image",
 *     ),
 *    @OA\Property(
 *         property="description",
 *         type="array",
 *         @OA\Items(type="string"),
 *         description="Description of the image",
 *     ),
 *    @OA\Property(
 *         property="alt_text",
 *         type="array",
 *         @OA\Items(type="string"),
 *         description="Alternative text of the image if image is broken",
 *     ),
 * )
 */
class StoreMediaRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           
            'image.*' => 'required|image|mimes:jpeg,png,jpg,gif', // Adjust max file size as needed
            'name.*' => 'required|sometimes|string|max:255',
            'caption.*' => 'required|sometimes|string|max:255',
            'description.*' => 'required|sometimes|string|max:255',
            'alt_text.*'=>'required|sometimes|string'

        ];
    }
}
