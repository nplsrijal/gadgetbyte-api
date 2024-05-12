<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AttributeResource",
 *     type="object",
 *     title="Attribute Resource",
 *     description="Attribute resource representation",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The menu display name",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The unique slug  of the Attribute.",
 *         example="/dashboard"
 *     ),
 *     @OA\Property(
 *         property="is_active",
 *         type="string",
 *         description="Enable/Disable of attributes ",
 *         example="Y/N"
 *     ),
 *     @OA\Property(
 *         property="values",
 *         type="json",
 *         description="The Values in json format",
 *         example="["100","200"," 300"]"
 *     )
 * )
 */
class AttributeResource extends JsonResource
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
