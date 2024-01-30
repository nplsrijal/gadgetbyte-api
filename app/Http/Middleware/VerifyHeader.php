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
        if (!$request->hasHeader('X-User-ID')) {
            $response = [
                'status' => 'error',
                'code' => '401',
                'message' => 'X-User-ID header is missing',
            ];
            return response()->json($response, Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
