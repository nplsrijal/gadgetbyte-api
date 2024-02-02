<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="StorePermissionRequest",
 *     type="object",
 *     title="Menu Permission  Store Request",
 *     description="Menu Permission store request data",
 *     required={"menu_id","user_type_id"},
 *     @OA\Property(
 *         property="menu_id",
 *         type="integer",
 *         description="The menu Id",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="user_type_id",
 *         type="integer",
 *         description="The User Type's Id",
 *         example="1"
 *     ),
 * )
 */
class StorePermissionRequest extends FormRequest
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
            "menu_id"=>"required|integer|max:10",
            "user_type_id"=>"required|integer|max:10",
        ];
    }
}
