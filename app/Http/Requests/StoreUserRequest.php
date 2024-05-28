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
 *     @OA\Property(
 *         property="facebook_url",
 *         type="string",
 *         description="The user's facebook profile link",
 *         example="www.fb.com/abc"
 *     ),
 *     @OA\Property(
 *         property="instagram_url",
 *         type="string",
 *         description="The user's instagram profile link",
 *         example="www.instagram.com/abc"
 *     ),
 *     @OA\Property(
 *         property="linkedin_url",
 *         type="string",
 *         description="The user's linkedin profile link",
 *         example="www.linkedin.com/abc"
 *     ),
 *     @OA\Property(
 *         property="google_url",
 *         type="string",
 *         description="The user's google profile link",
 *         example="www.google.com/abc"
 *     ),
 *     @OA\Property(
 *         property="twitter_url",
 *         type="string",
 *         description="The user's twitter profile link",
 *         example="www.twitter.com/abc"
 *     ),
 *     @OA\Property(
 *         property="youtube_url",
 *         type="string",
 *         description="The user's youtube profile link",
 *         example="www.youtube.com/abc"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The user's short introduction description",
 *         example="This is me, I am doing thiss......"
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
            "description"=>"required|sometimes|string",
            "facebook_url"=>"required|sometimes|string",
            "instagram_url"=>"required|sometimes|string",
            "linkedin_url"=>"required|sometimes|string",
            "google_url"=>"required|sometimes|string",
            "twitter_url"=>"required|sometimes|string",
            "youtube_url"=>"required|sometimes|string",
            "user_type_id"=>"required|numeric|max:255",

        ];
    }
}
