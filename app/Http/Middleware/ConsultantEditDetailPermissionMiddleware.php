<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class ConsultantEditDetailPermissionMiddleware
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
            if ($request->consultant == null) {

                return authUserInRole(['super-yonetici', 'yonetici']) ? $next($request) : redirect('/');
            }

            $url = $request->url();

            if (str_contains($url, "consultant")) {
                $consultant = User::find($request->consultant);
            }
            if (!$consultant) {
                return redirect('/');
            }

            if ($user && (authUserInRole(['super-yonetici', 'yonetici']) || $user->id == $consultant->id)) {
                return $next($request);
            }
            if ($user && $user->office_id == $consultant->office_id) {
                return $next($request);
            }

            return redirect('/');
        }

        return $next($request);
    }
}
