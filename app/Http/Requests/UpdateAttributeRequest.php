<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="UpdateAttributeRequest",
 *     type="object",
 *     title="Attribute  Update Request",
 *     description="Attribute  update request data",
 *     required={"name","slug","is_active","values"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The menu display name",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The unique slug  of the Attribute.",
 *         example="/dashboard"
 *     ),
 *     @OA\Property(
 *         property="is_active",
 *         type="string",
 *         description="Enable/Disable of attributes ",
 *         example="Y/N"
 *     ),
 *     @OA\Property(
 *         property="values",
 *         type="json",
 *         description="The Values in json format",
 *         example="["100","200"," 300"]"
 *     )
 * )
 */
class UpdateAttributeRequest extends FormRequest
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
            "slug"=>"required|string|max:255",
            "is_active"=>"required|string|max:255",
            "values"=>"required|string",
        ];
    }
}
