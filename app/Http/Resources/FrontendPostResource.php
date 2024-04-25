<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="FrontendPostResource",
 *     type="object",
 *     title="Post Resource",
 *     description="Post resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the post",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The display title of the post",
 *         example="Samsung Phone Nepal"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The Slug of the post",
 *         example="samsung-phones-nepal"
 *     ),
 *     @OA\Property(
 *         property="short_description",
 *         type="string",
 *         description="The Short Description of the post",
 *         example="short description goes here"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The Description of the post",
 *         example="Description goes here"
 *     ),
 *     @OA\Property(
 *         property="isprice",
 *         type="string",
 *         description="The Price flag of the post",
 *         example="Y"
 *     ),
 *     @OA\Property(
 *         property="show_in_pricelist",
 *         type="string",
 *         description="The Pricelist flag of the post",
 *         example="Y"
 *     ),
 *     @OA\Property(
 *         property="featured_image",
 *         type="string",
 *         description="The Image of the post",
 *         example="https://gadgetbyte.com/logo.png"
 *     ),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         @OA\Items(type="string"),
 *         description="Categories of the Post",
 *     ),
 * )
 */

class FrontendPostResource extends JsonResource
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
