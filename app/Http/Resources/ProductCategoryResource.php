<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProductCategoryResource",
 *     type="object",
 *     title="Product Category Resource",
 *     description="Product Category resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the post category",
 *         example="1"
 *     ),
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
class ProductCategoryResource extends JsonResource
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
