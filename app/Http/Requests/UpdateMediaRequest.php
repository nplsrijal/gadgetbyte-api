<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="UpdateMediaRequest",
 *     type="object",
 *     title="Media  Update Request",
 *     description="Media  update request data",
 *     required={"name","caption","description","alt_text"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The media display name",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="caption",
 *         type="string",
 *         description="The caption  of the Media.",
 *         example="apple-14"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The media's description ",
 *         example="This is the description of the media"
 *     ),
 *     @OA\Property(
 *         property="alt_text",
 *         type="string",
 *         description="The Alternative text of the media",
 *         example="apple-14-image"
 *     ),
 * )
 */
class UpdateMediaRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'caption' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'alt_text'=>'required|string'
        ];
    }
}
