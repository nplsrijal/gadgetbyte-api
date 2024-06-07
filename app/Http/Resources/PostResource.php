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
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Status for Draft,Review",
 *         example="D for Draft,R for Review"
 *     ),
 *     @OA\Property(
 *         property="post_on",
 *         type="string",
 *         description="Date & time to be published on",
 *         example="2024-05-14 13:55:12"
 *     ),
 *             @OA\Property(
 *                 property="seo_title_facebook",
 *                 type="string",
 *                 description="The seo title field for fb  of the post review",
 *                 example="Apple 14 launched in Nepal."
 *             ),
 *             @OA\Property(
 *                 property="seo_description_facebook",
 *                 type="string",
 *                 description="The seo description field for fb  of the post review",
 *                 example="Apple 14 launched in Nepal."
 *             ),
 *             @OA\Property(
 *                 property="seo_title_instagram",
 *                 type="string",
 *                 description="The seo title field for instagram  of the post review",
 *                 example="Apple 14 launched in Nepal."
 *             ),
 *              @OA\Property(
 *                 property="seo_description_instagram",
 *                 type="string",
 *                 description="The seo description field for instagram  of the post review",
 *                 example="Apple 14 launched in Nepal."
 *             ),
 *             @OA\Property(
 *                 property="seo_title_twitter",
 *                 type="string",
 *                 description="The seo title field for twitter  of the post review",
 *                 example="Apple 14 launched in Nepal."
 *             ),
 *             @OA\Property(
 *                 property="seo_description_twitter",
 *                 type="string",
 *                 description="The seo description field for twitter  of the post review",
 *                 example="Apple 14 launched in Nepal."
 *             ),
 *     @OA\Property(
 *         property="reviews",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             title="Post Review",
 *             description="Post review details",
 *             required={"review_id","title", "review"},
 *             @OA\Property(
 *                 property="review_id",
 *                 type="integer",
 *                 description="The id of the review",
 *                 example="1"
 *             ),
 *             @OA\Property(
 *                 property="title",
 *                 type="string",
 *                 description="The Text of the review",
 *                 example="Design"
 *             ),
 *             @OA\Property(
 *                 property="review",
 *                 type="string",
 *                 description="The review of the post",
 *                 example="Impressive."
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 description="The description of the post review",
 *                 example="Impressive."
 *             ),
 *         ),
 *         description="Array of post review details",
 *     ),
 *     @OA\Property(
 *         property="faqs",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             title="FAQ",
 *             description="FAQ details",
 *             required={"question","answer"},
 *             @OA\Property(
 *                 property="question",
 *                 type="string",
 *                 description="The question field",
 *                 example="How much price of Iphone?"
 *             ),
 *             @OA\Property(
 *                 property="answer",
 *                 type="string",
 *                 description="The answer field",
 *                 example="Its price is 45,000"
 *             ),
 *         ),
 *         description="Array of FAQ details",
 *     )
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
