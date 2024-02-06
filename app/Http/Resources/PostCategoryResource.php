<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="PostCategoryResource",
 *     type="object",
 *     title="Post Category Resource",
 *     description="Post Category resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the post category",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The Post Category display name",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The Endpoint  of the PostCategory.",
 *         example="apple"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The Post Category's description ",
 *         example="this is the description of it."
 *     ),
 *     @OA\Property(
 *         property="order_by",
 *         type="integer",
 *         description="The Priority of Post Category",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="integer",
 *         description="If Sub  Category, Send Post Category Id else 0",
 *         example="1"
 *     ),
 * )
 */
class PostCategoryResource extends JsonResource
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
