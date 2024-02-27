<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="StorePostReviewRequest",
 *     type="object",
 *     title="Store Post Review Request",
 *     description="Request data for storing post reviews",
 *     required={"post_id", "reviews"},
 *     @OA\Property(
 *         property="post_id",
 *         type="integer",
 *         description="The ID of the post",
 *     ),
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
 *         ),
 *         description="Array of post review details",
 *     ),
 * )
 */

class StorePostReviewRequest extends FormRequest
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
            'reviews.*.title' => 'required|string',
            'reviews.*.review_id' => 'required|integer',
            'reviews.*.review' => 'required|string',

        ];
    }
}
