<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;

class ApiMiddleware
{
    public function handle($request, Closure $next)
    {
        if ($request->is('api/*')) {
            if ($request->hasHeader('authorization')) {
                $bearertoken = $request->bearerToken();
                $token = $request->header('authorization');

                if ($request->hasHeader('accept') == null) {
                    info('client');
                    return app(CheckClientCredentials::class)->handle($request, $next);
                }
                
                if (!empty($bearertoken)) {
                    info('auth');
                    return app(Authenticate::class)->handle($request, function ($request) use ($next) {
                        return $next($request);
                    }, 'api');
                }
            }
        }

        return $next($request);
    }
}