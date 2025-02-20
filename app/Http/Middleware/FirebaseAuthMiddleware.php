<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FirebaseAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');
        $token = $this->extractTokenFromHeader($header);

        if (!$token) {
            return $next($request);
        }

        try {
            $secretKey = config('app.jwt.secret');
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            // $decoded = JWT::decode($token, env('FIREBASE_PUBLIC_KEY'), ['RS256']);
            $user = User::where('email', $decoded->email)->first();

            if ($user) {
                Auth::login($user, true);
            }
        } catch (\Exception $e) {
            // Hata durumunda oturumu açma
        }

        return $next($request);
    }

    private function extractTokenFromHeader($header)
    {
        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
