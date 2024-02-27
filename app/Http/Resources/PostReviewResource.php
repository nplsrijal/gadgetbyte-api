<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;



/**
 * @OA\Schema(
 *     schema="PostReviewResource",
 *     type="object",
 *     title="Post Review Resource",
 *     description="Post Review resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the post review",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="post_id",
 *         type="integer",
 *         description="The Id of the post ",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="review_id",
 *         type="integer",
 *         description="The Id of the Review",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="review",
 *         type="string",
 *         description="The review of the post",
 *         example="Very Nice."
 *     ),
 * )
 */
class PostReviewResource extends JsonResource
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
