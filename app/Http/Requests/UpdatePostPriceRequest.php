<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="UpdatePostPriceRequest",
 *     type="object",
 *     title="Post Price  Update Request",
 *     description="Post Price  update request data",
 *     required={"title","price"},
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The Post Price display name",
 *         example="Samsung galaxy S23 Ultra"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="float",
 *         description="The Amount of the  PostPrice.",
 *         example="10000"
 *     ),
 * )
 */
class UpdatePostPriceRequest extends FormRequest
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
            "title"=>"required|string",
            "price"=>"required|numeric"
        ];
    }
}
