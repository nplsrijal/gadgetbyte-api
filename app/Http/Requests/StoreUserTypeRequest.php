<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="StoreUserTypeRequest",
 *     type="object",
 *     title="User Type Store Request",
 *     description="User Type store request data",
 *     required={"name"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The usertype's name",
 *         example="Admin"
 *     ),
 * )
 */
class StoreUserTypeRequest extends FormRequest
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

        ];
    }
}
