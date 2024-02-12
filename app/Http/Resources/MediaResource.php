<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="MediaResource",
 *     type="object",
 *     title="Media Resource",
 *     description="Media resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the media",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="image",
 *         type="string",
 *         description="The image name of the media",
 *         example="image.png"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The display name of the media",
 *         example="Lenevo"
 *     ),
 *     @OA\Property(
 *         property="caption",
 *         type="string",
 *         description="The caption of the media",
 *         example="Lenevo"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The Description of the media",
 *         example="This is the description to lenevo best laptop in nepal"
 *     ),
 * )
 */
class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ResourceData = $this->resource;
        $data= $ResourceData;

        // remove not to be sent data;
        // unset($data['updated_by']);
        // unset($data['updated_at']);
        // unset($data['archived_at']);
        // unset($data['archived_by']);


        return $data;
    }
}
