<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="StoreLikeRequest",
 *     type="object",
 *     title="Like  Store Request",
 *     description="Like  store request data",
 *     required={"is_like"},
 *    @OA\Property(
 *         property="is_like",
 *         type="string",
 *         description="true/false/null",
 *     ),
 * )
 */
class StoreLikeRequest extends FormRequest
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
            'is_like'=>'required'

        ];
    }
}
