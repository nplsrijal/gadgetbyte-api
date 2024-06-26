<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="StoreBrandRequest",
 *     type="object",
 *     title="Brand  Store Request",
 *     description="Brand  store request data",
 *     required={"name","slug","image","order_by"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The Brand display name",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The Endpoint  of the Brand.",
 *         example="apple"
 *     ),
 *     @OA\Property(
 *         property="image",
 *         type="string",
 *         description="The Image Link ",
 *         example="this is the link of image."
 *     ),
 *     @OA\Property(
 *         property="order_by",
 *         type="integer",
 *         description="The Priority of Brand",
 *         example="1"
 *     ),
 * )
 */
class StoreBrandRequest extends FormRequest
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
            "name"=>"required|string",
            "slug"=>"required|string",
            "image"=>"required|string",
            "order_by"=>"required|integer",

        ];
    }
}
