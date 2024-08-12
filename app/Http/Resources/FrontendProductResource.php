<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="FrontendProductResource",
 *     type="object",
 *     title="Frontend Product Resource",
 *     description="Frontend Product resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the product",
 *         example="1"
 *     ),
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
 *         property="image_url",
 *         type="string",
 *         description="Image",
 *         example="https://gadgetbyte.com/logo.png"
 *     ),
 *     @OA\Property(
 *         property="is_active",
 *         type="string",
 *         description="Status for Active/Inactive",
 *         example="Y/N"
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
 *         description="Array of post attributes details"
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
 *         description="Array of FAQ details"
 *     )
 * )
 */
class FrontendProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ResourceData = $this->resource;
        $data= $ResourceData->toArray();

        // remove not to be sent data;
        unset($data['updated_by']);
        unset($data['updated_at']);
        unset($data['archived_at']);
        unset($data['archived_by']);


        return $data;
    }
}
