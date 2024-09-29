<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="UpdateSpecificationRequest",
 *     type="object",
 *     title="Specification Update Request",
 *     description="Specification  update request data",
 *     required={"name","image"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The Specification display name",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="image",
 *         type="string",
 *         description="The Image Link ",
 *         example="this is the link of image."
 *     ),
 * )
 */
class UpdateSpecificationRequest extends FormRequest
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
            "name"=>"required|string",
            "image"=>"required|string",
        ];
    }
}
