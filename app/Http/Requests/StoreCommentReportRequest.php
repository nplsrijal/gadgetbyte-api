<?php

namespace App\Http\Requests;


/**
 * @OA\Schema(
 *     schema="StoreCommentReportRequest",
 *     type="object",
 *     title="Comment Report  Store Request",
 *     description="Comment Report  store request data",
 *     required={"description"},
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The report description",
 *         example="This is dummy data"
 *     ),
 * )
 */
class StoreCommentReportRequest extends FormRequest
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
            "description"=>"required|string",

        ];
    }
}
