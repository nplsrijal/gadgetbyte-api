<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="StorePostImageRequest",
 *     type="object",
 *     title="Store Post Image Request",
 *     description="Request data for storing post images",
 *     required={"post_id", "slug", "images"},
 *     @OA\Property(
 *         property="post_id",
 *         type="integer",
 *         description="The ID of the post",
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The unique images slug for post image",
 *     ),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             title="Post Image",
 *             description="Post image details",
 *             required={"title", "image"},
 *             @OA\Property(
 *                 property="title",
 *                 type="string",
 *                 description="The title of the post",
 *                 example="Samsung Galaxy S21 Pro"
 *             ),
 *             @OA\Property(
 *                 property="image",
 *                 type="number",
 *                 format="string",
 *                 description="The image path",
 *                 example="/post/post.png"
 *             ),
 *         ),
 *         description="Array of post image details",
 *     ),
 * )
 */

class StorePostImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'post_id'=>'required|integer',
            'slug' => 'required|string',
            'images.*.image' => 'required|string',
            'images.*.title' => 'required|string',

        ];
    }
}
