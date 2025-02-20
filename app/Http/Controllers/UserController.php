<?php

namespace App\Http\Controllers;

use App\Events\UserRegisteredEvent;
use App\Models\Follower;
use App\Models\Portfolio;
use App\Models\User;
use App\Models\UserVerify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Orchid\Platform\Models\Role;
use Orchid\Support\Facades\Toast;

class UserController
{
    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Kullanıcı bulunamadı',
            ]);
        }

        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Şifre yanlış',
            ]);
        }
        $user->push_token = $request->push_token;
        $user->save();

        $user->role = $user->getRoles()[0]['slug'];
        $user->role_permissions = $user->getRoles()[0]['permissions'];
        $user->load('attachment');
        $return = [
            'status' => true,
            'message' => 'Giriş başarılı',
            'user' => $user->toArray(),
        ];

        $return['user']['jwt'] = $this->jwtCreate(['id' => $user->id, 'email' => $user->email]);

        if ($user->role == 'bireysel-danisman' || $user->role == 'ofis-danismani') {
            $return['user']['followers'] = Follower::where('to', $user->id)->pluck('from')->toArray();
            $return['user']['followings'] = Follower::where('from', $user->id)->pluck('to')->toArray();
        }

        return response()->json($return);
    }

    public function me(Request $request)
    {
        $authorizationHeader = explode(' ', $request->header('Authorization'));
        $jwt = isset($authorizationHeader[1]) ? $authorizationHeader[1] : false;

        $secretKey = config('app.jwt.secret');
        $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));

        // Kafa karıştırmaması için jwt keyini siliyoruz
        if (isset($decoded->jwt)) {
            unset($decoded->jwt);
        }
        $user = User::find($decoded->id);
        $user->role = $user->getRoles()[0]['slug'];
        $user->role_permissions = $user->getRoles()[0]['permissions'];
        $user->load('attachment');
        return response()->json([
            'status' => true,
            'message' => 'Oturum başarılı',
            'user' => $user
        ]);
    }

    public function jwtCheck()
    {
        return response()->json([
            'status' => true,
            'message' => 'Oturum başarılı',
        ]);
    }

    // public function logout(Request $request)
    // {
    //     //Find User
    //     $user = User::find($request->userId);
    //     //If No User
    //     if (!$user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Kullanıcı bulunamadı',
    //         ]);
    //     }

    //     $user->jwt = null;
    //     $user->pushToken = null;

    //     if (!$user->save()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Çıkış yaparken bir hata oluştu',
    //             "errorCode" => "user_CIKIS",
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Çıkış başarılı',
    //     ]);
    // }

    public function jwtCreate($user)
    {
        $jwt = JWT::encode([
            ...(is_array($user) ? $user : $user->toArray()),
            'exp' => time() + (60 * 60 * 24 * 30),
            'iat' => time()
        ], config('app.jwt.secret'), 'HS256');

        return $jwt;
    }

    public function restoreAndGetUser($user)
    {
        $user->restore();
        $user = User::where('email', $user->email)->first();
        $user->roles()->detach();
        $user->email_verified_at = null;
        return $user;
    }
    public function register(Request $request)
    {
        if ($request->password != $request->password_confirmation) {

            return redirect()->back()->withInput()->withErrors(['password' => __('Passwords do not match.')]);
        }

        $user = User::where('email', $request->email)->withTrashed()->first();
        $isUserTrashed = $user ? $user->trashed() : false;
        if ($user && !$user->trashed()) {
            return redirect()->back()->withInput()->withErrors(['email' => __('This email is already registered.')]);
        }

        $user = $user ? $this->restoreAndGetUser($user) : new User();


        $userRole = Role::where('slug', 'bireysel-danisman')->first();
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->province_id = $request->province_id;
        $user->state_id = $request->state_id;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        if ($isUserTrashed) {
        }
        $user->save();
        $user->roles()->attach($userRole);
        event(new UserRegisteredEvent($user));
        $isUserTrashed ?
            Toast::success(__('Welcome back. Please check your email inbox for verification.')) :
            Toast::success(__('You have been registered successfully. Please check your email inbox for verification.'));
        return redirect()->route('platform.login');
    }

    public function detail(Request $request)
    {
        // Find User
        $user = User::find($request->userId);
        //If No User
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Kullanıcı bulunamadı',
            ]);
        }
        // Append Full Name
        $user->append('full_name');
        // Append Full Address
        $user->append('full_address');
        // Define Counts of Records
        $user->counts = countsOfUserRecords($user->id);
        // Define Portfolios
        $userPortfolios = Portfolio::where('user_id', $user->id)->get();
        foreach ($userPortfolios as $key => $portfolio) {
            $portfolioAttachments = $portfolio->attachment()->get()->toArray();
            $result = array_map(function ($attachment) {
                return [
                    'mime' => $attachment['mime'],
                    'extension' => $attachment['extension'],
                    'url' => $attachment['url'],
                    'name' => $attachment['name'],
                    'id' => $attachment['id'],
                ];
            }, $portfolioAttachments);

            $portfolio->attachments = $result;
        }

        $user->portfolios = $userPortfolios;

        // Define User Avatar
        // Get User Avatar
        $userAvatar = $user->attachment()->get()->toArray();
        // Specify the fields to be returned
        $result = array_map(function ($attachment) {
            return [
                'mime' => $attachment['mime'],
                'extension' => $attachment['extension'],
                'url' => $attachment['url'],
                'name' => $attachment['name'],
                'id' => $attachment['id'],
            ];
        }, $userAvatar);

        // Add the avatar to the user object
        $user->attachments = $result;

        $user->role = $user->getRoles()[0]['slug'];
        $user->role_permissions = $user->getRoles()[0]['permissions'];

        return response()->json([
            'status' => true,
            'message' => 'Kullanıcı bulundu',
            'user' => $user
        ]);
    }

    public function verify(Request $request, $token)
    {
        $verifyQuery = UserVerify::with('user')->where('token', $token);
        $verifyFind = $verifyQuery->first();

        if (!is_null($verifyFind)) {
            $user = $verifyFind->user;

            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
                $user->save();
                $verifyQuery->delete();
                $message = __('Your email has been verified. You can login now.');
            } else {
                $message = __('Your email has already been verified. You can login now.');
            }

            Toast::info($message);

            return redirect()->route('platform.login');
        } else {
            Toast::error('Doğrulama kodu bulunamadı.');
        }
    }
}
