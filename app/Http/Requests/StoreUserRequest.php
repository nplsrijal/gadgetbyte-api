<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="StoreUserRequest",
 *     type="object",
 *     title="User  Store Request",
 *     description="User  store request data",
 *     required={"firstname","lastname","email","username","password"},
 *     @OA\Property(
 *         property="firstname",
 *         type="string",
 *         description="The user's first name",
 *         example="Admin"
 *     ),
 *     @OA\Property(
 *         property="lastname",
 *         type="string",
 *         description="The user's last name",
 *         example="Admin"
 *     ),
 *     @OA\Property(
 *         property="username",
 *         type="string",
 *         description="The user's username",
 *         example="admin1"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="email",
 *         description="The user's email address",
 *         example="admin@gbn.com"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         description="The user's password",
 *         example="admin@123"
 *     ),
 *    @OA\Property(
 *         property="user_type_id",
 *         type="integer",
 *         description="The user's usertype id",
 *         example="1"
 *     ),
 * )
 */
class StoreUserRequest extends FormRequest
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
            "firstname"=>"required|string|max:255",
            "lastname"=>"required|string|max:255",
            "username"=>"required|string|max:255",
            "email"=>"required|string|max:255",
            "password"=>"required|string|max:255",
            "user_type_id"=>"required|numeric|max:255",

        ];
    }
}
