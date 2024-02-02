<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="PermissionResource",
 *     type="object",
 *     title="Menu Permission Resource",
 *     description="Menu Permission resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the menupermission",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="menu_id",
 *         type="integer",
 *         description="The menu's id",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="user_type_id",
 *         type="integer",
 *         description="The User type's id",
 *         example="1"
 *     ),
 * )
 */
class PermissionResource extends JsonResource
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
