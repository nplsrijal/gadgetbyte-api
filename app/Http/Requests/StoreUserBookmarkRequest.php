<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreUserBookmarkRequest",
 *     type="object",
 *     title="Bookmark  Store Request",
 *     description="Bookmark  store request data",
 *     required={"post_id"},
 *     @OA\Property(
 *         property="post_id",
 *         type="integer",
 *         description="The id of the post",
 *         example="1"
 *     ),
 * )
 */
class StoreUserBookmarkRequest extends FormRequest
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
            "post_id"=>"required|integer",

        ];
    }
}
