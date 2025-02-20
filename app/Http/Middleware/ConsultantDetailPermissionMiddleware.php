<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class ConsultantDetailPermissionMiddleware
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
        if ($request->method() == 'GET') {

            $user = $request->user();
            if ($user->inRole('super-yonetici', 'yonetici')) {
                return $next($request);
            }

            if ($user->inRole('ofis-yoneticisi')) {
                $consultant = User::find($request->consultant);
                if ($consultant->office_id == $user->office_id) {
                    return $next($request);
                }
            }

            $consultant = User::find($request->consultant);
            if ($consultant->visibility) {
                return $next($request);
            }

            return redirect()->back();
        }

        return $next($request);
    }
}
