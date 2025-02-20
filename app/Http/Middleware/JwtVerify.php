<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtVerify
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
        $authorizationHeader = explode(' ', $request->header('Authorization'));
        $head = isset($authorizationHeader[0]) ? $authorizationHeader[0] : false;
        $jwt = isset($authorizationHeader[1]) ? $authorizationHeader[1] : false;

        if (!$head || !$jwt) {
            return response()->json([
                'status' => false,
                'message' => 'Geçersiz kullanıcı!',
                'errorCode' => 'JWT_INVALID_HEADER'
            ]);
        }

        try {
            $secretKey = config('app.jwt.secret');
            $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));

            // Kafa karıştırmaması için jwt keyini siliyoruz
            if (isset($decoded->jwt)) {
                unset($decoded->jwt);
            }

            auth()->login(User::find($decoded->id));

            return $next($request);
        } catch (ExpiredException $e) {
            $user = @User::find($decoded->id);

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kullanıcı bulunamadı'
                ]);
            }

            $user->jwt = null;
            $user->pushToken = null;

            if (!$user->save()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Giriş yaparken bir hata oluştu',
                    "errorCode" => "KULLANICI_TEMIZLEME",
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Oturum süreniz doldu! Lütfen tekrar giriş yapın.',
                'errorCode' => 'JWT_EXPIRED',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Geçersiz Kullanıcı!',
                'error' => $e->getMessage(),
                'errorCode' => 'JWT_EXCEPTION',
                'errorLine' => $e->getLine(),
            ], 400);
        }
    }
}
