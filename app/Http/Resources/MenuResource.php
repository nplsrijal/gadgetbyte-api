<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="MenuResource",
 *     type="object",
 *     title="Menu Resource",
 *     description="Menu resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the menu",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The display name of the menu",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="url",
 *         type="string",
 *         description="The Endpoint of the menu",
 *         example="/dashboard"
 *     ),
 *     @OA\Property(
 *         property="icon",
 *         type="string",
 *         description="The icon of the menu",
 *         example="fa fa-cogs"
 *     ),
 *     @OA\Property(
 *         property="order_by",
 *         type="integer",
 *         description="The Priority of menu",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="integer",
 *         description="If Sub menu, Send Menu Id else 0",
 *         example="1"
 *     ),
 * )
 */
class MenuResource extends JsonResource
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
