<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="StoreBlindCameraTestRequest",
 *     type="object",
 *     title="Blind Camera Test  Store Request",
 *     description="Blind Camera Test  store request data",
 *     required={"product_a_title","product_b_title","cover_image","product_a_images","product_b_images"},
 *     @OA\Property(
 *         property="product_a_title",
 *         type="string",
 *         description="The display name of the product a",
 *         example="Apple Iphone 14"
 *     ),
 *     @OA\Property(
 *         property="product_b_title",
 *         type="string",
 *         description="The display name of the product b.",
 *         example="Samsung Galaxy S24"
 *     ),
 *     @OA\Property(
 *         property="cover_image",
 *         type="string",
 *         description="The image path ",
 *         example="https://img.gadgetbyte.com"
 *     ),
 *     @OA\Property(
 *         property="product_a_images",
 *         type="string",
 *         description="The Values in json",
 *         example=""
 *     ),
 *     @OA\Property(
 *         property="product_b_images",
 *         type="string",
 *         description="The Values in json",
 *         example=""
 *     ),
 * )
 */
class StoreBlindCameraTestRequest extends FormRequest
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
            "product_a_title"=>"required|string",
            "product_b_title"=>"required|string",
            "cover_image"=>"required|string",
            "product_a_images"=>"required",
            "product_b_images"=>"required",
        ];
    }
}