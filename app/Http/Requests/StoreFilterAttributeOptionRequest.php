<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="StoreFilterAttributeOptionRequest",
 *     type="object",
 *     title="Category Attribute Option  Store Request",
 *     description="Category Attribute Option  store request data",
 *     required={"name","slug","values","is_active"},
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The Slug of the Category",
 *         example="mobile"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The display name",
 *         example="Dashboard"
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
class StoreFilterAttributeOptionRequest extends FormRequest
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
            "slug"=>"required|string|max:255",
            "name"=>"required|string|max:255",
            "values"=>"required",
            "is_active"=>"required|string|max:255",
        ];
    }
}
