<?php

namespace App\Http\Requests;

/**
 * @OA\Schema(
 *     schema="UpdateVariationRequest",
 *     type="object",
 *     title="Variation Update Request",
 *     description="Variation  update request data",
 *     required={"name","type"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The Variation display name",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="The type  of the Variation.",
 *         example="apple"
 *     ),
 * )
 */
class UpdateVariationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
            "type"=>"required|string",
          

        ];
    }
}
