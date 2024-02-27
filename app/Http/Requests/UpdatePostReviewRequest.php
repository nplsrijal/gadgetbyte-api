<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="UpdatePostReviewRequest",
 *     type="object",
 *     title="Post Review  Update Request",
 *     description="Post Review  update request data",
 *     required={"review_id","title","review"},
 *     @OA\Property(
 *         property="review_id",
 *         type="integer",
 *         description="The Id of The Review",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The Title of The Review",
 *         example="Design"
 *     ),
 *     @OA\Property(
 *         property="review",
 *         type="string",
 *         description="The Review of the  Post.",
 *         example="Very Impressive"
 *     ),
 * )
 */
class UpdatePostReviewRequest extends FormRequest
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
            "review_id"=>"required|integer",
            "title"=>"required|string",
            "review"=>"required|string",
        ];
    }
}
