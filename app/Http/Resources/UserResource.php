<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     title="User Resource",
 *     description="User resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the user",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="firstname",
 *         type="string",
 *         description="The first name of the user",
 *         example="Admin"
 *     ),
 *     @OA\Property(
 *         property="lastname",
 *         type="string",
 *         description="The last name of the user",
 *         example="Admin"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="email",
 *         description="The email of the user",
 *         example="admin@gbn.com"
 *     ),
 *     @OA\Property(
 *         property="username",
 *         type="string",
 *         description="The username of the user",
 *         example="admin1"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         description="The password of the user",
 *         example="admin@1"
 *     ),
 *     @OA\Property(
 *         property="user_type_id",
 *         type="integer",
 *         description="The User Type of the user",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="created_by",
 *         type="integer",
 *         description="user created by",
 *         example="7"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         description="user created at ",
 *         example="2023-06-21 08:25:18.000"
 *     )
 * )
 */
class UserResource extends JsonResource
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
