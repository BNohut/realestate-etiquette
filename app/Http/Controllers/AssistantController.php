<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Orchid\Platform\Models\Role;

class AssistantController
{
    public function list(Request $request)
    {
        try {
            $page = $request->page;
            $itemsPerPage = $request->itemsPerPage;
            if (authUserInRole(['super-yonetici', 'yonetici'])) {
                $users = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                    ->join('roles', 'role_users.role_id', '=', 'roles.id')
                    ->where('roles.slug', 'ofis-asistani')->select('users.*')->paginate($itemsPerPage, ['*'], 'page', $page);
            } else {
                $users = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                    ->join('roles', 'role_users.role_id', '=', 'roles.id')
                    ->where('roles.slug', 'ofis-asistani')
                    ->where('office_id', auth()->user()->office_id)->select('users.*')->paginate($itemsPerPage, ['*'], 'page', $page);
            }

            if ($users->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }
            $users->each(function ($user) {
                $user->append('office_name');
            });
            return response([
                'status' => true,
                'config' => ['page' => $page, 'itemsPerPage' => $itemsPerPage],
                'message' => "Asistanlar aktarımı başarılı.",
                'data' => $users
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function detail(Request $request)
    {
        try {
            $user = User::where('id', $request->userId)->first();
            if (!$user) {
                return response([
                    'status' => false,
                    'message' => "Asistan bulunamadı."
                ], 404);
            }
            return response([
                'status' => true,
                'message' => "Asistan bilgi aktarımı başarılı.",
                'data' => $user
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $isThereAnySoftDeletedUserWithThatEmail = User::onlyTrashed()->where('email', $request->email)->first();
            if ($isThereAnySoftDeletedUserWithThatEmail) {
                $user = $isThereAnySoftDeletedUserWithThatEmail;
                $user->restore();
            } else {
                $user = new User();
            }

            $user->fill($request->all());
            $password = $request->password;
            // $nonce = $request->nonce;
            // $tag = $request->tag;
            $user->when($request->filled('password'), function (Builder $builder) use ($password) {
                $builder->getModel()->password = Hash::make($password);
            });
            unset($password);
            if (authUserInRole('ofis-yoneticisi')) {
                $user->office_id = auth()->user()->office_id;
            }
            if (authUserInRole(['super-yonetici', 'yonetici'])) {
                $user->office_id = $request->office_id;
            }
            $user->email_verified_at = now();
            $user->office_approved_at = now();
            $user->save();

            $role = Role::firstWhere('slug', 'ofis-asistani');
            $user->roles()->attach($role);
            $user->save();
            $user->append('office_name');
            return response([
                'status' => true,
                'message' => "Asistan oluşturuldu.",
                'data' => $user
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $user = User::find($request->userId);
            if (!$user) {
                return response([
                    'status' => false,
                    'message' => "Asistan bulunamadı."
                ], 404);
            }
            if ($request->password) {
                if ($request->input('password') != $request->input('password_confirmation')) {
                    return response([
                        'status' => false,
                        'message' => "Şifreler eşleşmiyor."
                    ], 400);
                }
            }
            $user->fill($request->all());
            $password = $request->password;
            // $nonce = $request->nonce;
            // $tag = $request->tag;
            $user->when($request->filled('password'), function (Builder $builder) use ($password) {
                $builder->getModel()->password = Hash::make($password);
            });
            unset($password);
            if ($request->office_id) {
                if (authUserInRole(['super-yonetici', 'yonetici'])) {
                    $user->office_id = $request->office_id;
                } else {
                    return response([
                        'status' => false,
                        'message' => "Yetkiniz yok."
                    ], 403);
                }
            }

            $user->save();

            return response([
                'status' => true,
                'message' => "Asistan güncellendi.",
                'data' => $user
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function dismiss(Request $request)
    {
        try {
            $user = User::find($request->userId);
            if (!$user) {
                return response([
                    'status' => false,
                    'message' => "Asistan bulunamadı."
                ], 404);
            }
            $user->roles()->detach();
            $user->delete();
            return response([
                'status' => true,
                'message' => "Asistan azledildi.",
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function scope()
    {
        try {
            $users = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                ->join('roles', 'role_users.role_id', '=', 'roles.id')
                ->where('roles.slug', 'ofis-asistani')
                ->where('office_id', auth()->user()->office_id)->select('users.*')->get();
            $users->each(function ($user) {
                $user->append('office_name');
            });
            if ($users->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Ofisinize bağlı asistan bulunamadı"
                ], 404);
            }
            return response([
                'status' => true,
                'message' => "Ofisinize bağlı asistanlar başarıyla listelendi",
                'data' => $users
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
