<?php

namespace App\Http\Middleware;

use App\Models\Contact;
use Closure;
use Illuminate\Http\Request;

class ContactEditDetailPermissionMiddleware
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

            if ($request->contact == null) {
                return redirect()->back();
            }

            $url = $request->url();

            if (str_contains($url, "contact")) {
                $contact = Contact::find($request->contact);
            }
            if (!$contact) {
                return redirect('/');
            }

            $user = $request->user();
            if ($user && (authUserInRole(['super-yonetici', 'yonetici', 'ofis-yoneticisi']) || $user->id == $contact->user_id)) {
                return $next($request);
            }

            return redirect('/');
        }

        return $next($request);
    }
}
