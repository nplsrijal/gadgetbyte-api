<?php

namespace App\Http\Requests;


class StorePostRequest extends FormRequest
{
 /**
 * @OA\Schema(
 *     schema="StorePostRequest",
 *     type="object",
 *     title="Post Store Request",
 *     description="Post store request data",
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
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         @OA\Items(type="integer"),
 *         description="Ids of the category",
 *     ),
 *     @OA\Property(
 *         property="seo_title",
 *         type="string",
 *         description="SEO title",
 *         example="This is the title for SEO"
 *     ),
 *     @OA\Property(
 *         property="seo_description",
 *         type="string",
 *         description="Description for seo",
 *         example="This is description for SEO"
 *     ),
 *     @OA\Property(
 *         property="featured_image",
 *         type="string",
 *         description="Image",
 *         example="https://gadgetbyte.com/logo.png"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Status for Draft,Review",
 *         example="D for Draft,R for Review"
 *     ),
 *     @OA\Property(
 *         property="post_on",
 *         type="string",
 *         description="Date & time to be published on",
 *         example="2024-05-14 13:55:12"
 *     ),
 *     @OA\Property(
 *         property="reviews",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             title="Post Review",
 *             description="Post review details",
 *             required={"review_id","title", "review"},
 *             @OA\Property(
 *                 property="review_id",
 *                 type="integer",
 *                 description="The id of the review",
 *                 example="1"
 *             ),
 *             @OA\Property(
 *                 property="title",
 *                 type="string",
 *                 description="The Text of the review",
 *                 example="Design"
 *             ),
 *             @OA\Property(
 *                 property="review",
 *                 type="string",
 *                 description="The review of the post",
 *                 example="Impressive."
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 description="The description of the post review",
 *                 example="Impressive."
 *             ),
 *         ),
 *         description="Array of post review details",
 *     )
 * )
 */

//    @OA\Property(
//           property="tags",
//          type="array",
//          @OA\Items(type="integer"),
//           description="Ids of the tag",
//       ),

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
            'featured_image' => 'required|sometimes|string',
           // "category_id"=>"required|integer",
            //"sub_category_id"=>"required|integer",
            "isprice"=>"required|string",
            "show_in_pricelist"=>"required|string",
            "seo_title"=>"required|string",
            "seo_description"=>"required|string",
            "status"=>"required|sometimes|string",
            "post_on"=>"required|sometimes|string",
            //"tags.*"=>"required|sometimes|integer"
            "categories.*"=>"required|sometimes|integer",
            "reviews.*"=>"required|sometimes",
            "reviews.*.title"=>"required|sometimes",
            "reviews.*.review_id"=>"required|sometimes",
            "reviews.*.review"=>"required|sometimes",
            "reviews.*.description"=>"required|sometimes",

        ];
    }
}
