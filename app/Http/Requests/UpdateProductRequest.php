<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="UpdateProductRequest",
 *     type="object",
 *     title="Product Update Request",
 *     description="Product update request data",
 *     required={"title","slug","description","short_description","brand_id","categories","is_active","image_url","expert_rating","posts","videos"},
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The Product Title",
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
 *         description="The Product's short description",
 *         example="this is the short description of it."
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The Product's description",
 *         example="this is the description of it."
 *     ),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         @OA\Items(type="integer"),
 *         description="Ids of the category"
 *     ),
 *     @OA\Property(
 *         property="posts",
 *         type="array",
 *         @OA\Items(type="integer"),
 *         description="Ids of the posts",
 *     ),
 *     @OA\Property(
 *         property="videos",
 *         type="array",
 *         @OA\Items(type="string"),
 *         description="Youtube Video URL",
 *     ),
 *     @OA\Property(
 *         property="image_url",
 *         type="string",
 *         description="Image",
 *         example="https://gadgetbyte.com/logo.png"
 *     ),
 *     @OA\Property(
 *         property="expert_rating",
 *         type="string",
 *         description="Rating of the Product",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="is_active",
 *         type="string",
 *         description="Status for Active/Inactive",
 *         example="Y/N"
 *     ),
 *     @OA\Property(
 *         property="seo_title",
 *         type="string",
 *         description="The seo title field ",
 *         example="Apple 14"
 *     ),
 *     @OA\Property(
 *         property="seo_description",
 *         type="string",
 *         description="The seo description field",
 *         example="Apple 14"
 *     ),
 *     @OA\Property(
 *         property="seo_title_facebook",
 *         type="string",
 *         description="The seo title field for fb of the post review",
 *         example="Apple 14 launched in Nepal."
 *     ),
 *     @OA\Property(
 *         property="seo_description_facebook",
 *         type="string",
 *         description="The seo description field for fb of the post review",
 *         example="Apple 14 launched in Nepal."
 *     ),
 *     @OA\Property(
 *         property="seo_title_twitter",
 *         type="string",
 *         description="The seo title field for twitter of the post review",
 *         example="Apple 14 launched in Nepal."
 *     ),
 *     @OA\Property(
 *         property="seo_description_twitter",
 *         type="string",
 *         description="The seo description field for twitter of the post review",
 *         example="Apple 14 launched in Nepal."
 *     ),
 *     @OA\Property(
 *         property="attributes",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             title="Product Attributes",
 *             description="Product Attributes details",
 *             required={"attribute_option_id","attribute_option_name","values"},
 *             @OA\Property(
 *                 property="attribute_option_id",
 *                 type="integer",
 *                 description="The id of the attribute option",
 *                 example="1"
 *             ),
 *             @OA\Property(
 *                 property="attribute_option_name",
 *                 type="string",
 *                 description="The Text of the attribute option name",
 *                 example="Design"
 *             ),
 *             @OA\Property(
 *                 property="values",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 description="The values of attribute option",
 *                 example="[2gb,4gb]"
 *             ),
 *         ),
 *         description="Array of product attributes details"
 *     ),
 *     @OA\Property(
 *         property="variations",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             title="Variations",
 *             description="Variations details",
 *             required={"name","values"},
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 description="The name of variation",
 *                 example="Color"
 *             ),
 *             @OA\Property(
 *                 property="values",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 description="The values field",
 *                 example="[Green,Red]"
 *             ),
 *         ),
 *         description="Array of product variations details"
 *     )
 * )
 */

class UpdateProductRequest extends FormRequest
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
            "title"=>"required|string|max:255",
            "slug"=>"required|string|max:255",
            "short_description"=>"required|string",
            "description"=>"required|string",
            "brand_id"=>"required|integer",
            "is_active"=>"required|string",
            "image_url"=>"required|string",
            "expert_rating"=>"required|string",
            "seo_title"=>"required|sometimes|string",
            "seo_description"=>"required|sometimes|string",
            "seo_title_facebook"=>"required|sometimes|string",
            "seo_description_facebook"=>"required|sometimes|string",
            "seo_title_twitter"=>"required|sometimes|string",
            "seo_description_twitter"=>"required|sometimes|string",
            "categories.*"=>"required|sometimes|integer",
            "attributes.*"=>"required|sometimes",
            "attributes.*.attribute_option_id"=>"required|sometimes",
            "attributes.*.attribute_option_name"=>"required|sometimes",
            "attributes.*.values"=>"required|sometimes",
            "variations.*"=>"required|sometimes",
            "variations.*.name"=>"required|sometimes",
            "variations.*.values"=>"required|sometimes",
            "product_variants.*"=>"required|sometimes",

        ];
    }
}
