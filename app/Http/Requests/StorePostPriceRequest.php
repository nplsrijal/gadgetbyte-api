<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="StorePostPriceRequest",
 *     type="object",
 *     title="Store Post Price Request",
 *     description="Request data for storing post prices",
 *     required={"post_id", "price"},
 *     @OA\Property(
 *         property="post_id",
 *         type="integer",
 *         description="The ID of the post",
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             title="Post Price",
 *             description="Post price details",
 *             required={"title", "amount"},
 *             @OA\Property(
 *                 property="title",
 *                 type="string",
 *                 description="The title of the post",
 *                 example="Samsung Galaxy S21 Pro"
 *             ),
 *             @OA\Property(
 *                 property="amount",
 *                 type="number",
 *                 format="float",
 *                 description="The price amount",
 *                 example="10000.00"
 *             ),
 *         ),
 *         description="Array of post price details",
 *     ),
 * )
 */

class StorePostPriceRequest extends FormRequest
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
            'price' => 'required|array',
            'price.*.title' => 'required|string',
            'price.*.amount' => 'required|numeric|min:0',

        ];
    }
}
