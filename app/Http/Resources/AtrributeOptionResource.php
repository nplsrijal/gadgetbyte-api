<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AttributeOptionResource",
 *     type="object",
 *     title="Attribute Option Resource",
 *     description="Attribute Option resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the attribute option",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The display name",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="attribute_id",
 *         type="string",
 *         description="The Id  of the Attribute.",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="values",
 *         type="string",
 *         description="The Values in json",
 *         example=""
 *     ),
 *     @OA\Property(
 *         property="is_active",
 *         type="string",
 *         description="Enable/Disable of attributes ",
 *         example="Y/N"
 *     )
 * )
 */
class AtrributeOptionResource extends JsonResource
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
