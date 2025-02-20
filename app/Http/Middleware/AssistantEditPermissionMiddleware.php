<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class AssistantEditPermissionMiddleware
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

            if ($request->user == null) {
                return redirect()->back();
            }

            $url = $request->url();

            if (str_contains($url, "assistant")) {
                $user = User::find($request->user);
            }
            if (!$user || !$user->inRole('ofis-asistani')) {
                return redirect('/');
            }

            $authUser = $request->user();
            if ($user && (authUserInRole(['super-yonetici', 'yonetici']))) {
                return $next($request);
            }
            if ($user && (authUserInRole(['ofis-yoneticisi']) && $user->office_id == $authUser->office_id)) {
                return $next($request);
            }
            if ($user && (authUserInRole(['ofis-asistani']) && $user->id == $authUser->id)) {
                return $next($request);
            }

            return redirect('/');
        }

        return $next($request);
    }
}
