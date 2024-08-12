<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;



/**
 * @OA\Schema(
 *     schema="CommentResource",
 *     type="object",
 *     title="Comment Resource",
 *     description="Comment resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the comment",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="body",
 *         type="string",
 *         description="The comments text",
 *         example="Samsung Phone Nepal"
 *     ),
 *     @OA\Property(
 *         property="commentable_type",
 *         type="string",
 *         description="The type of the comments done for",
 *         example="Post"
 *     ),
 *     @OA\Property(
 *         property="commentable_id",
 *         type="integer",
 *         description="The ids of the comments done for",
 *         example="1"
 *     ),
 * )
 */
class CommentResource extends JsonResource
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
