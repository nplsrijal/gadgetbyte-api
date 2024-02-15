<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="UpdateMenuRequest",
 *     type="object",
 *     title="Menu  Update Request",
 *     description="Menu  update request data",
 *     required={"name","url","icon","order_by","parent_id"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The menu display name",
 *         example="Dashboard"
 *     ),
 *     @OA\Property(
 *         property="url",
 *         type="string",
 *         description="The Endpoint  of the Menu.",
 *         example="/dashboard"
 *     ),
 *     @OA\Property(
 *         property="icon",
 *         type="string",
 *         description="The menu's icon ",
 *         example="fa fa-cogs"
 *     ),
 *     @OA\Property(
 *         property="order_by",
 *         type="integer",
 *         description="The Priority of menu",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="integer",
 *         description="If Sub menu, Send Menu Id else 0",
 *         example="1"
 *     ),
 * )
 */
class UpdateMenuRequest extends FormRequest
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
            "name"=>"required|string|max:255",
            "url"=>"required|string|max:255",
            "icon"=>"required|string|max:255",
            "order_by"=>"required|integer|max:255",
            "parent_id"=>"required|integer|max:255",

        ];
    }
}
