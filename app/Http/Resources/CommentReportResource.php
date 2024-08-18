<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CommentReportResource",
 *     type="object",
 *     title="Comment Report Resource",
 *     description="Comment Report resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the comment's report",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="comment_id",
 *         type="integer",
 *         description="The Id of the commented comment id",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The report text done by user",
 *         example="This is dummy text."
 *     )
 * )
 */
class CommentReportResource extends JsonResource
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
