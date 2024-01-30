<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;



/**
 * @OA\Schema(
 *     schema="UserTypeResource",
 *     type="object",
 *     title="UserType Resource",
 *     description="UserType resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the usertype",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the User Type",
 *         example="Admin"
 *     ),
 *     @OA\Property(
 *         property="created_by",
 *         type="integer",
 *         description="User Type created by",
 *         example="7"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         description="User Type created at ",
 *         example="2023-06-21 08:25:18.000"
 *     )
 * )
 */
class UserTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $UserType = $this->resource;
        $usertype= $UserType->toArray();

        // remove not to be sent data;
        unset($usertype['updated_by']);
        unset($usertype['updated_at']);
        unset($usertype['archived_at']);
        unset($usertype['archived_by']);


        return $usertype;

    }
}
