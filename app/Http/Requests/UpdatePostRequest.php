<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
 * @OA\Schema(
 *     schema="StoreUpdateRequest",
 *     type="object",
 *     title="Post Update Request",
 *     description="Post update request data",
 *     required={"title","slug","description","short_description","category_id","sub_category_id","isprice","show_in_pricelist"},
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The Post Title",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The Slug for the post",
 *         example="apple"
 *     ),
 *     @OA\Property(
 *         property="short_description",
 *         type="string",
 *         description="The Post's short description ",
 *         example="this is the short description of it."
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The Post's description ",
 *         example="this is the description of it."
 *     ),
 *     @OA\Property(
 *         property="category_id",
 *         type="integer",
 *         description="The  Post Category",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="sub_category_id",
 *         type="integer",
 *         description="The Post Sub Category",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="isprice",
 *         type="string",
 *         description="If Price then 'Y' else 'N'",
 *         example="Y"
 *     ),
 *     @OA\Property(
 *         property="show_in_pricelist",
 *         type="string",
 *         description="If Price to be shown for price list  then 'Y' else 'N'",
 *         example="Y"
 *     ),
 * )
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
            "title"=>"required|string|max:255",
            "slug"=>"required|string|max:255",
            "short_description"=>"required|string",
            "description"=>"required|string",
            "category_id"=>"required|integer",
            "sub_category_id"=>"required|integer",
            "isprice"=>"required|string",
            "show_in_pricelist"=>"required|string",

        ];
    }
}
