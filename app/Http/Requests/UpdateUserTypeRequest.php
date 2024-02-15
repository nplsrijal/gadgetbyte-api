<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="UpdateUserTypeRequest",
 *     type="object",
 *     title="User Type Update Request",
 *     description="User Type update request data",
 *     required={"name"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the bank",
 *         example="Admin"
 *     ),
 * )
 */
class UpdateUserTypeRequest extends FormRequest
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
