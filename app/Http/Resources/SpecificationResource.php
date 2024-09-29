<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="SpecificationResource",
 *     type="object",
 *     title="Specification Resource",
 *     description="Specification resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the vendor",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The Specification display name",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="image",
 *         type="string",
 *         description="The Image Link ",
 *         example="this is the link of image."
 *     )
 * )
 */
class SpecificationResource extends JsonResource
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
