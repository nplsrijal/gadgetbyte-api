<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="VendorResource",
 *     type="object",
 *     title="Vendor Resource",
 *     description="Vendor resource representation",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The Vendor display name",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The Endpoint  of the Vendor.",
 *         example="apple"
 *     ),
 *     @OA\Property(
 *         property="image",
 *         type="string",
 *         description="The Image Link ",
 *         example="this is the link of image."
 *     ),
 *       @OA\Property(
 *         property="website_url",
 *         type="string",
 *         description="The Website Link ",
 *         example="https://abc.com"
 *     ),
 *     @OA\Property(
 *         property="order_by",
 *         type="integer",
 *         description="The Priority of Vendor",
 *         example="1"
 *     )
 * )
 */
class VendorResource extends JsonResource
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
