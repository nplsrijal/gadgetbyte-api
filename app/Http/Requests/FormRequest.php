<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Contracts\Validation\Validator;

/**
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     title="Validation Error Response",
 *     description="An object representing a validation error response",
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="The error message",
 *         example="The given data was invalid."
 *     ),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         description="An object containing field validation error messages",
 *         example={
 *             "email": {
 *                 "The email field is required."
 *             }
 *         }
 *     )
 * )
 */
class FormRequest extends BaseFormRequest
{
    protected function failedValidation(Validator $validator)
    {       
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
