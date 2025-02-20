<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Orchid\Attachment\File;
use Orchid\Platform\Models\Role;

class OfficeController
{
    public function all()
    {
        try {
            $offices = Office::all(['id', 'name']);
            if ($offices->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }
            return response([
                'status' => true,
                'message' => "Ofisler aktarımı başarılı.",
                'data' => $offices
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong during listing offices.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $page = $request->page;
            $itemsPerPage = $request->itemsPerPage;

            $offices = Office::paginate($itemsPerPage, ['*'], 'page', $page);
            $offices->each(function ($office) {
                $office->append('manager_name');
            });
            if ($offices->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }

            foreach ($offices as $key => $office) {
                $officesAttachments = $office->attachment()->get()->toArray();
                $result = array_map(function ($attachment) {
                    return [
                        'mime' => $attachment['mime'],
                        'extension' => $attachment['extension'],
                        'url' => $attachment['url'],
                        'name' => $attachment['name'],
                        'id' => $attachment['id'],
                    ];
                }, $officesAttachments);

                $office->attachments = $result;
            }

            return response([
                'status' => true,
                'config' => ['page' => $page, 'itemsPerPage' => $itemsPerPage],
                'message' => "Ofis verileri aktarımı başarılı.",
                'data' => $offices
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong during listing offices.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function detail(Request $request)
    {
        try {
            $office = Office::find($request->officeId);
            $office->append('manager_name');
            if (!$office) {
                return response([
                    'status' => false,
                    'message' => "Ofis bulunamadı"
                ], 404);
            }
            $officeAttachment = $office->attachment()->get()->toArray();
            $result = array_map(function ($attachment) {
                return [
                    'mime' => $attachment['mime'],
                    'extension' => $attachment['extension'],
                    'url' => $attachment['url'],
                    'name' => $attachment['name'],
                    'id' => $attachment['id'],
                ];
            }, $officeAttachment);

            $office->attachments = $result;

            return response([
                'status' => true,
                'message' => "Ofis bilgisi aktarımı başarılı",
                'data' => $office
            ], 200);
        } catch (\Exception $error) {

            return response([
                'status' => false,
                'message' => "Something went wrong during get contact detail.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $isThereAnyOfficeWithUserId = Office::where('user_id', $request->user_id)->first();
            if ($isThereAnyOfficeWithUserId) {
                return response([
                    'status' => false,
                    'message' => "Bu kullanıcıya ait bir ofis zaten var."
                ], 400);
            }
            if ($request->file('logo')) {

                $file = new File($request->file('logo'));
                $attachment = $file->allowDuplicates()->load();

                $officeRequest = $request->all();
                $officeRequest['logo'] = $attachment;
                $request->replace($officeRequest);

                $newOffice = new Office();
                $newOffice
                    ->fill($request->toArray())
                    ->fill(['logo' => $attachment->id])
                    ->save();

                $newOffice->attachment()->syncWithoutDetaching(
                    $request->input('logo', [])
                );

                $officeAttachment = $newOffice->attachment()->get()->toArray();
                $result = array_map(function ($attachment) {
                    return [
                        'mime' => $attachment['mime'],
                        'extension' => $attachment['extension'],
                        'url' => $attachment['url'],
                        'name' => $attachment['name'],
                        'id' => $attachment['id'],
                    ];
                }, $officeAttachment);

                $newOffice->attachments = $result;
                $newOffice->append('manager_name');

                return response([
                    'status' => true,
                    'message' => "Office created with logo.",
                    'data' => $newOffice
                ], 200);
            } else {
                $newOffice = new Office();
                $officeRequest = $request->all();
                $officeRequest['logo'] = generateOfficeLogo($request->name);
                $request->replace($officeRequest);
                $newOffice
                    ->fill(collect($request->all())->except('logo')->toArray())
                    ->fill(['logo' => $officeRequest['logo']->id])
                    ->save();

                $newOffice->attachment()->syncWithoutDetaching(
                    $request->input('logo', [])
                );
                $officeAttachment = $newOffice->attachment()->get()->toArray();
                $result = array_map(function ($attachment) {
                    return [
                        'mime' => $attachment['mime'],
                        'extension' => $attachment['extension'],
                        'url' => $attachment['url'],
                        'name' => $attachment['name'],
                        'id' => $attachment['id'],
                    ];
                }, $officeAttachment);

                $newOffice->attachments = $result;
                $newOffice->append('manager_name');

                return response([
                    'status' => true,
                    'message' => "Office created with generated logo.",
                    'data' => $newOffice
                ], 200);
            }
        } catch (\Exception $error) {

            return response([
                'status' => false,
                'message' => "Something went wrong during creating office.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $office = Office::find((int) $request->officeId);
            $office->append('manager_name');
            if (!$office) {
                return response([
                    'status' => false,
                    'message' => "Ofis bulunamadı"
                ], 404);
            }

            if ($request->file('logo')) {
                if ($office->attachment()->first()) {
                    $office->attachment->each->delete();
                }

                $file = new File($request->file('logo'));
                $attachment = $file->allowDuplicates()->load();

                $officeRequest = $request->all();
                $officeRequest['logo'] = $attachment;
                $request->replace($officeRequest);

                $office
                    ->fill($request->all())
                    ->fill(['logo' => $attachment->id])
                    ->save();

                $office->attachment()->syncWithoutDetaching(
                    $request->input('logo', [])
                );

                $officeAttachment = $office->attachment()->get()->toArray();
                $result = array_map(function ($attachment) {
                    return [
                        'mime' => $attachment['mime'],
                        'extension' => $attachment['extension'],
                        'url' => $attachment['url'],
                        'name' => $attachment['name'],
                        'id' => $attachment['id'],
                    ];
                }, $officeAttachment);

                $office->attachments = $result;

                return response([
                    'status' => true,
                    'message' => "Office updated with logo.",
                    'data' => $office
                ], 200);
            } else {
                $office
                    ->fill($request->all())
                    ->save();

                $officeAttachment = $office->attachment()->get()->toArray();
                $result = array_map(function ($attachment) {
                    return [
                        'mime' => $attachment['mime'],
                        'extension' => $attachment['extension'],
                        'url' => $attachment['url'],
                        'name' => $attachment['name'],
                        'id' => $attachment['id'],
                    ];
                }, $officeAttachment);

                $office->attachments = $result;

                return response([
                    'status' => true,
                    'message' => "Office updated without logo.",
                    'data' => $office
                ], 200);
            }
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong during updating office.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $office = Office::find($request->officeId);

            if (!$office) {
                return response([
                    'status' => false,
                    'message' => "Ofis bulunamadı"
                ], 404);
            }
            // Manage Users of Office
            // SoftDelete OfficeManager
            // Find Office Manager
            $officeManager = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                ->join('roles', 'role_users.role_id', '=', 'roles.id')
                ->where('roles.slug', 'ofis-yoneticisi')
                ->where('users.office_id', $request->officeId)->first();
            // Delete Office Manager Attachments
            $officeManager->attachment->each->delete();
            // Delete Office Manager - it will be soft deleted
            $officeManager->delete();

            // Change Office Consultants Role to Individual Consultants
            // Get Approved Consultants
            $approvedConsultants = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                ->join('roles', 'role_users.role_id', '=', 'roles.id')
                ->where('roles.slug', 'ofis-danismani')
                ->where('office_id', $request->officeId)->whereNotNull('office_approved_at')->get();
            // Replace Role of Consultants
            foreach ($approvedConsultants as $consultant) {
                // Change User Role as 'Individual Consultant'
                $role = Role::where('slug', 'bireysel-danisman')->first();
                $consultant->replaceRoles(array($role->id));
                // Delete Office Information of Consultant & Save
                $consultant->office_id = null;
                $consultant->office_approved_at = null;
                $consultant->save();
                // Send Notification to Consultant
                Notification::send(
                    $consultant,
                    new SystemNotification(
                        "Ofis kapatıldı!",
                        $office->name . " kapatıldı. Artık bireysel danışman olarak devam edebilirsiniz.",
                        'platform.office'
                    )
                );
            }
            // Get Consultants That Who Wanted To Join Office But Not Approved Yet
            $consultants = User::where('office_id', $request->officeId)->whereNull('office_approved_at')->get();
            // Delete Office Information of Consultant & Save
            foreach ($consultants as $consultant) {
                $consultant->office_id = null;
                $consultant->save();
            }

            // Get Office Assistants
            $officeAssistants = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                ->join('roles', 'role_users.role_id', '=', 'roles.id')
                ->where('roles.slug', 'ofis-asistani')
                ->where('users.office_id', $request->officeId)->get();
            // Delete Office Assistant
            foreach ($officeAssistants as $assistant) {
                $assistant->delete();
            }
            // Delete Office Attachments
            $office->attachment->each->delete();
            // Delete Office
            $office->delete();

            return response([
                'status' => true,
                'message' => 'Ofis başarıyla silindi'
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function join(Request $request)
    {
        try {
            $user = $request->user();
            $user->office_id = $request->officeId;
            $user->save();

            //Send Notification to Office Manager
            //Find Office Object
            $office = Office::find($request->officeId);
            //Send Notification to Office Manager
            Notification::send(
                User::find($office->user_id),
                new SystemNotification(
                    "Katılım isteği alındı!",
                    $user->getFullNameAttribute() . " ofisinize katılma isteği gönderdi.",
                    'platform.office.consultant'
                )
            );
            return response([
                'status' => true,
                'message' => 'Ofis yöneticisine katılım isteği bildirildi'
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function cancel(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->office_id != null && $user->office_approved_at == null) {
                return response([
                    'status' => false,
                    'message' => 'Kullanıcının herhangi bir talebi yok'
                ], 400);
            }
            $user->office_id = null;
            $user->save();

            return response([
                'status' => true,
                'message' => 'Katılım isteği iptal edildi'
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request)
    {
        try {
            $user = User::find($request->userId);
            if ($request->user()->inRole('ofis-yoneticisi') && $user->office_id != $request->user()->office_id) {
                return response([
                    'status' => false,
                    'message' => 'Bu isteği onaylayamazsınız'
                ], 400);
            }

            if ($user->office_id == null && $user->office_approved_at == null) {
                return response([
                    'status' => false,
                    'message' => 'Kullanıcının herhangi bir talebi yok'
                ], 400);
            }
            $user->office_approved_at = now();
            $user->save();
            //Send Notification to Request Owner
            //Find Office Object
            $office = Office::where('user_id', $request->user()->id)->first();
            //Send Notification
            Notification::send(
                $user,
                new SystemNotification(
                    "Katılım isteğiniz onaylandı!",
                    $office->name . " katılım isteğinizi onayladı.",
                    'platform.office'
                )
            );

            return response([
                'status' => true,
                'message' => 'Katılım isteği onaylandı'
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request)
    {
        try {
            $user = User::find($request->userId);
            if ($request->user()->inRole('ofis-yoneticisi') && $user->office_id != $request->user()->office_id) {
                return response([
                    'status' => false,
                    'message' => 'Bu isteği reddedemezsiniz'
                ], 400);
            }
            if ($user->office_id == null && $user->office_approved_at == null) {
                return response([
                    'status' => false,
                    'message' => 'Kullanıcının herhangi bir talebi yok'
                ], 400);
            }
            $user->office_id = null;
            $user->save();
            //Send Notification to Request Owner
            //Find Office Object
            $office = Office::where('user_id', $request->user()->id)->first();
            //Send Notification
            Notification::send(
                $user,
                new SystemNotification(
                    "Katılım isteğiniz reddedildi!",
                    $office->name . " katılım isteğinizi reddetti.",
                    'platform.office'
                )
            );
            return response([
                'status' => true,
                'message' => 'Katılım isteği reddedildi'
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function dismiss(Request $request)
    {
        try {
            $user = User::find($request->userId);

            $user->office_id = null;
            $user->office_approved_at = null;
            $user->save();

            // Change User Role as 'Individual Consultant'
            $role = Role::where('slug', 'bireysel-danisman')->first();
            $user->replaceRoles(array($role->id));

            //Send Notification to Ex Office Consultant
            //Find Office Object
            $office = Office::where('user_id', $request->user()->id)->first();
            //Send Notification
            Notification::send(
                $user,
                new SystemNotification(
                    "Çıkarma işlemi algılandı!",
                    $office->name . " sizi danışmanları arasından çıkardı.",
                    'platform.office'
                )
            );
            return response([
                'status' => true,
                'message' => 'Danışman ofisten azledildi'
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function leave(Request $request)
    {
        try {
            $user = $request->user();
            $userOfficeId = $user->office_id;
            $user->office_id = null;
            $user->office_approved_at = null;
            $user->save();

            // Change User Role as 'Individual Consultant'
            $role = Role::where('slug', 'bireysel-danisman')->first();
            $user->replaceRoles(array($role->id));

            //Send Notification to Office Manager
            //Find Office Object
            $office = Office::find($userOfficeId);
            //Send Notification to Office Manager
            Notification::send(
                User::find($office->user_id),
                new SystemNotification(
                    "Aranızdan ayrılan oldu!",
                    $user->getFullNameAttribute() . " ofisinizden ayrıldı.",
                    'platform.office.consultant'
                )
            );
            return response([
                'status' => true,
                'message' => 'Ofisten ayrıldınız'
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function managers()
    {
        try {
            $managers = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                ->join('roles', 'role_users.role_id', '=', 'roles.id')
                ->leftJoin('offices', 'users.id', '=', 'offices.user_id')
                ->where('roles.slug', 'ofis-yoneticisi')
                ->whereNull('offices.user_id') // offices tablosunda kaydı olmayanları seç
                ->select('users.id', 'users.name', 'users.last_name')
                ->get();

            if ($managers->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Boşta olan bir yönetici bulunamadı."
                ], 404);
            }
            return response([
                'status' => true,
                'message' => "Ofis yöneticileri aktarımı başarılı.",
                'data' => $managers
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong during listing managers.",
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
