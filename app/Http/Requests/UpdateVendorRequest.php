<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="UpdateVendorRequest",
 *     type="object",
 *     title="Vendor Update Request",
 *     description="Vendor  update request data",
 *     required={"name","slug","image","website_url","order_by"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The Vendor display name",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="The Endpoint  of the Vendor.",
 *         example="apple"
 *     ),
 *     @OA\Property(
 *         property="image",
 *         type="string",
 *         description="The Image Link ",
 *         example="this is the link of image."
 *     ),
 *     @OA\Property(
 *         property="website_url",
 *         type="string",
 *         description="The Website url Link ",
 *         example="https://abc.com"
 *     ),
 *     @OA\Property(
 *         property="order_by",
 *         type="integer",
 *         description="The Priority of Vendor",
 *         example="1"
 *     ),
 * )
 */
class UpdateVendorRequest extends FormRequest
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
            "website_url"=>"required|sometimes|string",
            "order_by"=>"required|integer",

        ];
    }
}
