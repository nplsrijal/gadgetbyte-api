<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="PostResource",
 *     type="object",
 *     title="Post Resource",
 *     description="Post resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique id of the post",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The display title of the post",
 *         example="Samsung Phone Nepal"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The Slug of the post",
 *         example="samsung-phones-nepal"
 *     ),
 *     @OA\Property(
 *         property="short_description",
 *         type="string",
 *         description="The Short Description of the post",
 *         example="short description goes here"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The Description of the post",
 *         example="Description goes here"
 *     ),
 *     @OA\Property(
 *         property="seo_title",
 *         type="string",
 *         description="The Title of the post's SEO",
 *         example="Description goes here"
 *     ),
 *     @OA\Property(
 *         property="featured_image",
 *         type="string",
 *         description="The Image of the post"
 *     ),
 *     @OA\Property(
 *         property="seo_description",
 *         type="string",
 *         description="The Description of the post's SEO",
 *         example="Description goes here"
 *     ),
 *      @OA\Property(
 *          property="seo_title_social_media",
 *          type="string",
 *          description="The seo title field for social media  of the post review",
 *          example="Apple 14 launched in Nepal."
 *      ),
 * )
 */

//  *     @OA\Property(
//  *         property="category_id",
//  *         type="integer",
//  *         description="The Category of the post",
//  *         example="1"
//  *     ),
//  *     @OA\Property(
//  *         property="sub_category_id",
//  *         type="integer",
//  *         description="The Sub Category of the post",
//  *         example="3"
//  *     ),
class PostResource extends JsonResource
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
