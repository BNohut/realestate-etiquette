<?php

namespace App\Http\Middleware;

use App\Models\Portfolio;
use Closure;
use Illuminate\Http\Request;

class PortfolioEditPermissionMiddleware
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

            if ($request->portfolio == null) {
                return $next($request);
            }

            $url = $request->url();

            if (str_contains($url, "portfolio")) {
                $portfolio = Portfolio::find($request->portfolio);
            }
            if (!$portfolio) {
                return redirect('/');
            }

            $user = $request->user();
            if ($user && ($user->inRole('super-yonetici', 'yonetici') || $user->id == $portfolio->user_id)) {
                return $next($request);
            }

            if ($user && $user->inRole('ofis-yoneticisi')) {
                if ($portfolio->userS->office_id == $user->office_id) {
                    return $next($request);
                }
            }

            return redirect('/');
        }

        return $next($request);
    }
}
