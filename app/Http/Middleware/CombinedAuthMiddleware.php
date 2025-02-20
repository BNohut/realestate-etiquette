<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use PHPUnit\Util\Exception;

class CombinedAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Eğer kullanıcı oturum açmışsa, işlemi devam ettir
            return $next($request);
        }

        $token = $request->bearerToken();

        if (!$token) {
            // Token yoksa hata döndür
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            // Firebase JWT kütüphanesi ile tokeni doğrula
            $secretKey = config('app.jwt.secret');
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            // $decoded = JWT::decode($token, env('FIREBASE_PUBLIC_KEY'), ['RS256']);
        } catch (ExpiredException $e) {
            // Süresi dolmuş token için hata döndür
            return response()->json(['error' => 'Token expired'], 401);
        } catch (Exception $e) {
            // Geçersiz token için hata döndür
            return response()->json(['error' => 'Token invalid'], 401);
        }

        // Token doğrulandıktan sonra kullanıcıyı request'e ekle
        $request['user'] = $decoded->user;

        // İşlemi devam ettir
        return $next($request);
    }
}
