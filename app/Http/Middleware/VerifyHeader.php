<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next): Response
    {
        // Check if the request method is GET
        if ($request->method() !== 'GET' && !$request->hasHeader('X-User-ID')) {
            $response = [
                'status' => 'error',
                'code' => '401',
                'message' => 'X-User-ID header is missing',
            ];
            return response()->json($response, Response::HTTP_UNAUTHORIZED);
        }
        if (!$request->hasHeader('authorization')) {
            $response = [
                'status' => 'error',
                'code' => '401',
                'message' => 'Authorization Required',
            ];
            return response()->json($response, Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
