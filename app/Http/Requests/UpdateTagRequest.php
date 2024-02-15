<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="UpdateTagRequest",
 *     type="object",
 *     title="Tag  Update Request",
 *     description="Tag  update request data",
 *     required={"name","description"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The tag display name",
 *         example="Lenevo"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The Description  of the Tag.",
 *         example="Lenevo laptops in nepal"
 *     ),
 * )
 */
class UpdateTagRequest extends FormRequest
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
            "name"=>"required|string|max:255",
            "description"=>"required|string|max:255",

        ];
    }
}
