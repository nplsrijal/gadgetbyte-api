<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="StoreSocialLoginRequest",
 *     type="object",
 *     title="Social Login Store Request",
 *     description="Social Login store request data",
 *     required={"firstname","lastname","email","type"},
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
 *         property="email",
 *         type="email",
 *         description="The user's email address",
 *         example="admin@gbn.com"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="The Social login type",
 *         example="fb"
 *     ),
 *    @OA\Property(
 *         property="google_id",
 *         type="string",
 *         description="The user's unique google id",
 *         example="abcd-sj@google.com"
 *     ),
 *    @OA\Property(
 *         property="fb_id",
 *         type="string",
 *         description="The user's unique fb id",
 *         example="abcd-sj@fb.com"
 *     ),
 * )
 */
class StoreSocialLoginRequest extends FormRequest
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
            "email"=>"required|string|max:255",
            "type"=>"required|string",
            "fb_id"=>"required|sometimes",
            "google_id"=>"required|sometimes"

        ];
    }
}
