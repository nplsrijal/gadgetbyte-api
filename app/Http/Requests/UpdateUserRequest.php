<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="UpdateUserRequest",
 *     type="object",
 *     title="User  Update Request",
 *     description="User  update request data",
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
class UpdateUserRequest extends FormRequest
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
            "firstname"=>"required|string|max:30",
            "lastname"=>"required|string|max:30",
            "username"=>"required|string|max:15",
            "email"=>"required|string|max:50",
            "password"=>"required|string|max:20",
            "user_type_id"=>"required|integer|max:10",

        ];
    }
}
