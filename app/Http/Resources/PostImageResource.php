<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="PostImageResource",
 *     type="object",
 *     title="Post Image Resource",
 *     description="Post Image resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the post image",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The title of the post image",
 *         example="Samsung Galaxy S23"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The slug of the post image",
 *         example="Samsung Galaxy S23"
 *     ),
 *     @OA\Property(
 *         property="post_id",
 *         type="integer",
 *         description="The Id of the post ",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="image",
 *         type="string",
 *         description="The relative path of the post image",
 *         example="/post/post.png"
 *     ),
 * )
 */
class PostImageResource extends JsonResource
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
