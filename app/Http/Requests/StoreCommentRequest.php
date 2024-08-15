<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="StoreCommentRequest",
 *     type="object",
 *     title="Comment  Store Request",
 *     description="Comment  store request data",
 *     required={"body"},
 *     @OA\Property(
 *         property="body",
 *         type="string",
 *         description="The comment text",
 *         example="This is test"
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="integer",
 *         description="The parent's comment id (if comment reply to be done)",
 *         example="1"
 *     ),
 * )
 */
class StoreCommentRequest extends FormRequest
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
            'body' => 'required|string',
            'parent_id' => 'required|numeric|sometimes',

        ];
    }
}
