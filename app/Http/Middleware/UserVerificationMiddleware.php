<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Platform\Models\Role;

class UserVerificationMiddleware
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
        if ($request->user()->inRole('super-yonetici') || $request->user()->inRole('yonetici')) {
            return $next($request);
        }

        if ($request->user()->email_verified_at == null) {
            Auth::logout();
            return redirect()->route('platform.login')->withErrors(['verified' => __('Email or Admin Approval Required.')]);
        }


        return $next($request);
    }
}
