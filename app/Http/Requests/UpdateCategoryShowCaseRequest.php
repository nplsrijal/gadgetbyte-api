<?php

namespace App\Http\Requests;

/**
 * @OA\Schema(
 *     schema="UpdateCategoryShowCaseRequest",
 *     type="object",
 *     title="Category ShowCase Update Request",
 *     description="Category ShowCase update request data",
 *     required={"category_id","is_active","order_by"},
 *     @OA\Property(
 *         property="category_id",
 *         type="integer",
 *         description="The Category Id of the Product-category",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="is_active",
 *         type="string",
 *         description="Active/Inactive",
 *         example="Y"
 *     ),
 *     @OA\Property(
 *         property="order_by",
 *         type="integer",
 *         description="The Priority of Category",
 *         example="1"
 *     ),
 * )
 */
class UpdateCategoryShowCaseRequest extends FormRequest
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
            "category_id"=>"required|integer",
            "is_active"=>"required|string",
            "order_by"=>"required|integer",

        ];
    }
}
