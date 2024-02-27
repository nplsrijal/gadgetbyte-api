<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="PostPriceResource",
 *     type="object",
 *     title="Post Price Resource",
 *     description="Post Price resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the post price",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The title of the post price",
 *         example="Samsung Galaxy S23"
 *     ),
 *     @OA\Property(
 *         property="post_id",
 *         type="integer",
 *         description="The Id of the post ",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="float",
 *         description="The amount of the post price",
 *         example="10000"
 *     ),
 * )
 */
class PostPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ResourceData = $this->resource;
        $data= (is_array($ResourceData))?$ResourceData:$ResourceData->toArray();

        // remove not to be sent data;
        // unset($data['updated_by']);
        // unset($data['updated_at']);
        // unset($data['archived_at']);
        // unset($data['archived_by']);


        return $data;
    }
}
