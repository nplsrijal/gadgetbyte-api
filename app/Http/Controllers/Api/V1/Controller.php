<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

/**
 * @OA\Info(
 *     title="GadgetByteNepal",
 *     version="1.0.0",
 *     description="GadgetByte Nepal Rest Apis Documentation",
 * )
 */
class Controller extends BaseController
{
    protected $user;

    /**
     * Create a new Controller instance.
     *
     * This method sets up a middleware function that sets the `user` property of the Controller
     * to the currently authenticated user.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
    }

    /**
     * Generate a successful response.
     *
     * @param mixed $data The data to include in the response.
     * @param string $message The message to include in the response (default: "Success").
     * @param int $code The HTTP status code to use (default: 200).
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data, $message = "Success", $code = Response::HTTP_OK)
    {
        $response = [
            'status' => 'success',
            'code' => $code,
            'message' => $message,
        ];

        if ($data instanceof ResourceCollection && isset($data->response()->getData(true)['links'])) {
            $response['data'] = $data->response()->getData(true)['data'];
            $response['links'] = $data->response()->getData(true)['links'];
            $response['meta'] = $data->response()->getData(true)['meta'];
        } else {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Generate an error response.
     *
     * @param string $message The message to include in the response (default: "Error").
     * @param int $code The HTTP status code to use (default: 400).
     * @param array $errors An optional array of error details.
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($message = "Error", $code = Response::HTTP_BAD_REQUEST, $errors = [])
    {
        return response()->json([
            'status' => 'error',
            'code' => $code,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}
