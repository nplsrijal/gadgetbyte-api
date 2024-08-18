<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="BlindCameraTestResource",
 *     type="object",
 *     title="Blind Camera Test Resource",
 *     description="Blind Camera Test resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the blind camera test",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="product_a_title",
 *         type="string",
 *         description="The display name of the product a",
 *         example="Apple Iphone 14"
 *     ),
 *     @OA\Property(
 *         property="product_b_title",
 *         type="string",
 *         description="The display name of the product b.",
 *         example="Samsung Galaxy S24"
 *     ),
 *     @OA\Property(
 *         property="cover_image",
 *         type="string",
 *         description="The image path ",
 *         example="https://img.gadgetbyte.com"
 *     ),
 *     @OA\Property(
 *         property="product_a_images",
 *         type="string",
 *         description="The Values in json",
 *         example=""
 *     ),
 *     @OA\Property(
 *         property="product_b_images",
 *         type="string",
 *         description="The Values in json",
 *         example=""
 *     ),
 * )
 */
class BlindCameraTestResource extends JsonResource
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
