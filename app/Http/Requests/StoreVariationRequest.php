<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="StoreVariationRequest",
 *     type="object",
 *     title="Variation  Store Request",
 *     description="Variation  store request data",
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
class StoreVariationRequest extends FormRequest
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
            "type"=>"required|string",

        ];
    }
}
