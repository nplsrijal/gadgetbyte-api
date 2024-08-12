<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="CommentLikeResource",
 *     type="object",
 *     title="Comment Like Resource",
 *     description="Comment Like resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the like/dislike",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="The User's Id",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="comment_id",
 *         type="integer",
 *         description="The Id's  of the Comment.",
 *         example="1"
 *     ),
 * )
 */
class CommentLikeResource extends JsonResource
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
