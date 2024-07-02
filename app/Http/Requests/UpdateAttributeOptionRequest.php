<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="UpdateAttributeOptionRequest",
 *     type="object",
 *     title="Attribute Option  Update Request",
 *     description="Attribute Option  update request data",
 *     required={"name","attribute_id","values","is_active"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The display name",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="attribute_id",
 *         type="string",
 *         description="The Id  of the Attribute.",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="values",
 *         type="string",
 *         description="The Values in json",
 *         example=""
 *     ),
 *     @OA\Property(
 *         property="is_active",
 *         type="string",
 *         description="Enable/Disable of attributes ",
 *         example="Y/N"
 *     )
 * )
 */
class UpdateAttributeOptionRequest extends FormRequest
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
            "attribute_id"=>"required|integer",
            "values"=>"required|string",
            "is_active"=>"required|string|max:255",
        ];
    }
}
