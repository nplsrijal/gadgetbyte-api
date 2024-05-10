<?php

namespace App\Http\Requests;

/**
 * @OA\Schema(
 *     schema="UpdateProductCategoryRequest",
 *     type="object",
 *     title="Product Category  Update Request",
 *     description="Product Category  update request data",
 *     required={"name","slug","description","order_by","parent_id"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The Product Category display name",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The Endpoint  of the ProductCategory.",
 *         example="apple"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The Product Category's description ",
 *         example="this is the description of it."
 *     ),
 *     @OA\Property(
 *         property="image",
 *         type="string",
 *         description="The Product Category's image ",
 *         example="this is the image string url."
 *     ),
 *     @OA\Property(
 *         property="long_description",
 *         type="string",
 *         description="The Product Category's long description ",
 *         example="this is the description of it."
 *     ),
 *     @OA\Property(
 *         property="seo_title",
 *         type="string",
 *         description="The Product Category's seo title ",
 *         example="this is the title of it."
 *     ),
 *     @OA\Property(
 *         property="meta_description",
 *         type="string",
 *         description="The Product Category's meta description ",
 *         example="this is the meta description  of it."
 *     ),
 *     @OA\Property(
 *         property="order_by",
 *         type="integer",
 *         description="The Priority of Product Category",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="integer",
 *         description="If Sub  Category, Send Product Category Id else 0",
 *         example="1"
 *     ),
 * )
 */
class UpdateProductCategoryRequest extends FormRequest
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
            "description"=>"required|string|max:255",
            "image"=>"required|string|max:255",
            "long_description"=>"required|string",
            "seo_title"=>"required|string",
            "meta_description"=>"required|string",
            "order_by"=>"required|integer|max:255",
            "parent_id"=>"required|integer|max:255",

        ];
    }
}
