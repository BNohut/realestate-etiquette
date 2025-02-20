<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequestMethodMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if ($request->method() != 'POST') {
            return response([
                'status' => false,
                'message' => 'Bad Request',
            ], 400);
        }

        return $next($request);
    }
}
