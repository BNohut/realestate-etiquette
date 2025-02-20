<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Attachment\File;
use Orchid\Platform\Models\Role;

class ConsultantController
{
    public function all()
    {
        try {
            if (authUserInRole(['super-yonetici', 'yonetici'])) {
                $consultants = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                    ->join('roles', 'role_users.role_id', '=', 'roles.id')
                    ->whereIn('roles.slug', ['bireysel-danisman', 'ofis-danismani'])->select('users.id', 'users.name', 'users.last_name')->get();
            } elseif (authUserInRole(['ofis-yoneticisi', 'ofis-danismani'])) {
                $consultants = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                    ->join('roles', 'role_users.role_id', '=', 'roles.id')
                    ->where('roles.slug', 'ofis-danismani')
                    ->where('office_id', auth()->user()->office_id)->select('users.id', 'users.name', 'users.last_name')->get();
            }
            if ($consultants->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }

            return response([
                'status' => true,
                'message' => "Bireysel danışman verileri aktarımı başarılı.",
                'data' => $consultants
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong during listing consultants.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $page = $request->page;
            $itemsPerPage = $request->itemsPerPage;

            $consultants = User::leftJoin('role_users', 'users.id', '=', 'role_users.user_id')
                ->leftJoin('roles', 'role_users.role_id', '=', 'roles.id')
                ->whereIn('roles.slug', ['ofis-danismani', 'bireysel-danisman'])
                ->select('users.id', 'users.name', 'users.last_name', 'users.email', 'users.phone', 'users.visibility', 'users.url', 'users.province_id', 'users.state_id', 'users.neighborhood', 'users.json', 'users.avatar', 'users.office_id', 'users.permissions', 'users.about_me')
                ->paginate($itemsPerPage, ['*'], 'page', $page);

            if ($consultants->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }

            $consultants->each(function ($consultant) {
                $consultant->append('office_name');
            });

            foreach ($consultants as $key => $consultant) {
                $consultantAttachments = $consultant->attachment()->get()->toArray();
                $result = array_map(function ($attachment) {
                    return [
                        'mime' => $attachment['mime'],
                        'extension' => $attachment['extension'],
                        'url' => $attachment['url'],
                        'name' => $attachment['name'],
                        'id' => $attachment['id'],
                    ];
                }, $consultantAttachments);

                $consultant->attachments = $result;
            }
            return response([
                'status' => true,
                'config' => ['page' => $page, 'itemsPerPage' => $itemsPerPage],
                'message' => "Bireysel danışman verileri aktarımı başarılı.",
                'data' => $consultants
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong during listing consultants.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function detail(Request $request)
    {
        try {
            $consultant = User::find($request->userId);

            if (!$consultant) {
                return response([
                    'status' => false,
                    'message' => "Danışman bulunamadı"
                ], 404);
            }

            $consultantAttachment = $consultant->attachment()->get()->toArray();
            $result = array_map(function ($attachment) {
                return [
                    'mime' => $attachment['mime'],
                    'extension' => $attachment['extension'],
                    'url' => $attachment['url'],
                    'name' => $attachment['name'],
                    'id' => $attachment['id'],
                ];
            }, $consultantAttachment);

            $consultant->attachments = $result;

            return response([
                'status' => true,
                'message' => "Danışman bilgisi aktarımı başarılı",
                'data' => $consultant
            ], 200);
        } catch (\Exception $error) {

            return response([
                'status' => false,
                'message' => "Something went wrong during get consultant detail.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            if ($request->file('avatar')) {

                $file = new File($request->file('avatar'));
                $attachment = $file->allowDuplicates()->load();
                $consultantRequest = $request->all();
                $consultantRequest['avatar'] = $attachment;
                $request->replace($consultantRequest);

                $newConsultant = new User();

                $individualConsultantRole = Role::where('slug', 'bireysel-danisman')->first();

                $newConsultant->password = bcrypt('consultant');
                $newConsultant->permissions = $individualConsultantRole->permissions;

                $newConsultant
                    ->fill($request->toArray())
                    ->fill(['avatar' => $attachment->id])
                    ->save();

                $newConsultant->attachment()->syncWithoutDetaching(
                    $request->input('avatar', [])
                );

                $newConsultant->roles()->attach($individualConsultantRole);

                $consultantAttachment = $newConsultant->attachment()->get()->toArray();
                $result = array_map(function ($attachment) {
                    return [
                        'mime' => $attachment['mime'],
                        'extension' => $attachment['extension'],
                        'url' => $attachment['url'],
                        'name' => $attachment['name'],
                        'id' => $attachment['id'],
                    ];
                }, $consultantAttachment);

                $newConsultant->attachments = $result;

                return response([
                    'status' => true,
                    'message' => "Individual consultant has been created with avatar.",
                    'data' => $newConsultant
                ], 200);
            } else {
                $newConsultant = new User();
                $individualConsultantRole = Role::where('slug', 'bireysel-danisman')->first();
                $newConsultant->password = bcrypt('consultant');
                $newConsultant->permissions = $individualConsultantRole->permissions;

                $newConsultant
                    ->fill($request->all())
                    ->save();

                $newConsultant->roles()->attach($individualConsultantRole);

                $newConsultant->load('attachment');

                return response([
                    'status' => true,
                    'message' => "Individual consultant has been created without avatar.",
                    'data' => $newConsultant
                ], 200);
            }
        } catch (\Exception $error) {

            return response([
                'status' => false,
                'message' => "Something went wrong during creating contact.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $consultant = User::find((int) $request->userId);

            if (!$consultant) {
                return response([
                    'status' => false,
                    'message' => "Danışman bulunamadı"
                ], 404);
            }
            if ($request->file('avatar')) {
                if ($consultant->attachment()->first()) {
                    $consultant->attachment[0]->delete();
                }

                $file = new File($request->file('avatar'));
                $attachment = $file->allowDuplicates()->load();

                $consultantRequest = $request->all();
                $consultantRequest['avatar'] = $attachment;
                $request->replace($consultantRequest);

                $consultant
                    ->fill($request->all())
                    ->fill(['avatar' => $attachment->id])
                    ->save();

                $consultant->attachment()->syncWithoutDetaching(
                    $request->input('avatar', [])
                );

                $consultantAttachment = $consultant->attachment()->get()->toArray();
                $result = array_map(function ($attachment) {
                    return [
                        'mime' => $attachment['mime'],
                        'extension' => $attachment['extension'],
                        'url' => $attachment['url'],
                        'name' => $attachment['name'],
                        'id' => $attachment['id'],
                    ];
                }, $consultantAttachment);

                $consultant->attachments = $result;

                return response([
                    'status' => true,
                    'message' => "The consultant updated with avatar.",
                    'data' => $consultant
                ], 200);
            } else {
                $consultant
                    ->fill($request->all())
                    ->save();

                $consultant->load('attachment');

                return response([
                    'status' => true,
                    'message' => "The consultant updated without avatar.",
                    'data' => $consultant
                ], 200);
            }
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong during updating consultant.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $consultant = User::find($request->userId);

            if (!$consultant) {
                return response([
                    'status' => false,
                    'message' => "Danışman bulunamadı"
                ], 404);
            }
            $consultant->attachment->each->delete();
            $consultant->delete();

            return response([
                'status' => true,
                'message' => 'Danışman başarıyla silindi'
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function scope()
    {
        try {
            if (authUserInRole(['super-yonetici', 'yonetici'])) {
                $users = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                    ->join('roles', 'role_users.role_id', '=', 'roles.id')
                    ->whereIn('roles.slug', ['ofis-danismani', 'bireysel-danisman'])->whereNotNull('office_id')->select('users.*')->get();
            } else {
                $authenticatedUserOfficeId = Office::firstWhere('id', auth()->user()->office_id)->id;
                $users = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                    ->join('roles', 'role_users.role_id', '=', 'roles.id')
                    ->whereIn('roles.slug', ['ofis-danismani', 'bireysel-danisman'])
                    ->where('office_id', $authenticatedUserOfficeId)->select('users.*')->get();
            }
            $users->each(function ($user) {
                $user->append('office_name');
            });
            if ($users->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Ofisinize bağlı danışman bulunamadı"
                ], 404);
            }
            return response([
                'status' => true,
                'message' => "Ofisinize bağlı danışmanlar başarıyla listelendi",
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
