<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreUserBookmarkRequest",
 *     type="object",
 *     title="Bookmark  Store Request",
 *     description="Bookmark  store request data",
 *     required={"type","id"},
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="To distinguish for post/product",
 *         example="post"
 *     ),
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Id of the post/product",
 *         example="12"
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
            "type"=>"required|string",
            "id"=>"required|integer",

        ];
    }
}
