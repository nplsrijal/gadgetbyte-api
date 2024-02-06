<?php

namespace App\Http\Requests;

/**
 * @OA\Schema(
 *     schema="StorePostCategoryRequest",
 *     type="object",
 *     title="Post Category  Store Request",
 *     description="Post Category  store request data",
 *     required={"name","slug","description","order_by","parent_id"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The Post Category display name",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The Endpoint  of the PostCategory.",
 *         example="apple"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The Post Category's description ",
 *         example="this is the description of it."
 *     ),
 *     @OA\Property(
 *         property="order_by",
 *         type="integer",
 *         description="The Priority of Post Category",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="integer",
 *         description="If Sub  Category, Send Post Category Id else 0",
 *         example="1"
 *     ),
 * )
 */
class StorePostCategoryRequest extends FormRequest
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
            "name"=>"required|string|max:30",
            "slug"=>"required|string|max:30",
            "description"=>"required|string|max:100",
            "order_by"=>"required|integer|max:10",
            "parent_id"=>"required|integer|max:10",

        ];
    }
}
