<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="UserBookmarkResource",
 *     type="object",
 *     title="Bookmark Resource",
 *     description="Bookmark resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the tag",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="The id of the user",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="post_id",
 *         type="integer",
 *         description="The id of the post",
 *         example="1"
 *     ),
 * )
 */
class UserBookmarkResource extends JsonResource
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
