<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CategoryShowCaseResource",
 *     type="object",
 *     title="Category ShowCase Resource",
 *     description="Category ShowCase resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the vendor",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="category_id",
 *         type="integer",
 *         description="The Category Id of the Product-category",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="is_active",
 *         type="string",
 *         description="Active/Inactive",
 *         example="Y"
 *     ),
 *     @OA\Property(
 *         property="order_by",
 *         type="integer",
 *         description="The Priority of Category",
 *         example="1"
 *     )
 * )
 */
class CategoryShowCaseResource extends JsonResource
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
