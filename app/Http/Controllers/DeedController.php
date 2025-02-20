<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Record;
use App\Models\RecordType;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class DeedController
{
    public function listforoffice(Request $request)
    {
        try {
            $page = $request->page;
            $itemsPerPage = $request->itemsPerPage;

            $deedType = RecordType::where('name', 'Tapu Satış-Kiralama İşlemleri')->first()->id;
            $recordsQuery = Record::join('users', 'users.id', 'records.user_id')
                ->whereNotNull(['users.office_id', 'users.office_approved_at'])
                ->where('record_type_id', $deedType)
                ->withTrashed()
                ->orderBy('record_date', 'desc');
            if (authUserInRole(['ofis-yoneticisi'])) {
                $recordsQuery->where('users.office_id', auth()->user()->office_id);
            }
            $deeds = $recordsQuery->select('records.*')->paginate($itemsPerPage, ['*'], 'page', $page);
            $deeds->each(function ($deed) {
                $deed->append('office_name');
            });
            if ($deeds->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }

            return response([
                'status' => true,
                'config' => ['page' => $page, 'itemsPerPage' => $itemsPerPage],
                'message' => "Tapu satış-kiralama işlemleri verileri aktarımı başarılı.",
                'data' => $deeds
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }
    public function list(Request $request)
    {
        try {
            $page = $request->page;
            $itemsPerPage = $request->itemsPerPage;

            $deedType = RecordType::where('name', 'Tapu Satış-Kiralama İşlemleri')->first()->id;
            if (authUserInRole(['super-yonetici', 'yonetici'])) {
                $deeds = Record::where('record_type_id', $deedType)->paginate($itemsPerPage, ['*'], 'page', $page);
            } elseif (authUserInRole('ofis-yoneticisi')) {
                $deeds = Record::join('users', 'records.user_id', '=', 'users.id')
                    ->whereNotNull('records.approved_at')
                    ->where([['records.record_type_id', $deedType], ['users.office_id', auth()->user()->office_id]])->select('records.*')->paginate($itemsPerPage, ['*'], 'page', $page);
            } elseif (authUserInRole(['bireysel-danisman', 'ofis-danismani'])) {
                $deeds = Record::whereNotNull('approved_at')->where([['record_type_id', $deedType], ['user_id', auth()->user()->id]])->paginate($itemsPerPage, ['*'], 'page', $page);
            }


            if ($deeds->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }

            return response([
                'status' => true,
                'config' => ['page' => $page, 'itemsPerPage' => $itemsPerPage],
                'message' => "Tapu satış-kiralama işlemleri verileri aktarımı başarılı.",
                'data' => $deeds
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
            $deedType = RecordType::where('name', 'Tapu Satış-Kiralama İşlemleri')->first()->id;
            $deed = Record::find($request->recordId);

            if ($deed->record_type_id != $deedType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$deed) {
                return response([
                    'status' => false,
                    'message' => "Tapu işlem kaydı bulunamadı"
                ], 404);
            }

            return response([
                'status' => true,
                'message' => "Tapu satış-kiralama işlemi kaydı bilgisi aktarımı başarılı",
                'data' => $deed
            ], 200);
        } catch (\Exception $error) {

            return response([
                'status' => false,
                'message' => __('Something went wrong'),
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $deedType = RecordType::where('name', 'Tapu Satış-Kiralama İşlemleri')->first()->id;
            $deed = new Record();
            $deed->fill($request->all());
            $deed->record_type_id = $deedType;
            if (User::find($request->user_id)->inRole('bireysel-danisman')) {
                $deed->approved_at = now();
            } else {
                // Send Notification
                // Find Manager of Office
                $officeId = User::find($request->user_id)->office_id;
                $office = Office::find($officeId);
                //Sent Notification to Office Manager
                Notification::send(
                    User::find($office->user_id),
                    new SystemNotification(
                        "Tapu Satış-Kiralama Kaydı Bilgisi Alındı!",
                        User::find($request->user_id)->getFullNameAttribute() . " tapu satış-kiralama kaydı oluşturdu.",
                        'platform.office.deed'
                    )
                );
            }
            $deed->save();

            return response([
                'status' => true,
                'message' => "Tapu satış-kiralama işlemi kaydı başarıyla oluşturuldu.",
                'data' => $deed
            ], 200);
        } catch (\Exception $error) {

            return response([
                'status' => false,
                'message' => __('Something went wrong'),
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $deedType = RecordType::where('name', 'Tapu Satış-Kiralama İşlemleri')->first()->id;
            $deed = Record::find((int) $request->recordId);

            if ($deed->record_type_id != $deedType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$deed) {
                return response([
                    'status' => false,
                    'message' => "Tapu satış-kiralama işlemi kaydı bulunamadı"
                ], 404);
            }

            if ($deed->approved_at != null || $deed->deleted_at != null) {
                return response([
                    'status' => false,
                    'message' => "Onaylanmış/Reddedilmiş bir kayıt güncellenemez."
                ], 400);
            }
            $deed
                ->fill($request->all())
                ->save();


            return response([
                'status' => true,
                'message' => "Tapu satış-kiralama işlemi kaydı güncellendi",
                'data' => $deed
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => __('Something went wrong'),
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $deedType = RecordType::where('name', 'Tapu Satış-Kiralama İşlemleri')->first()->id;
            $deed = Record::find($request->recordId);

            if ($deed->record_type_id != $deedType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$deed) {
                return response([
                    'status' => false,
                    'message' => "Tapu satış-kiralama işlem kaydı bulunamadı"
                ], 404);
            }

            $deed->delete();

            return response([
                'status' => true,
                'message' => 'Tapu satış-kiralama işlem kaydı başarıyla silindi'
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => __('Something went wrong'),
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request)
    {
        try {

            $deedType = RecordType::where('name', 'Tapu Satış-Kiralama İşlemleri')->first()->id;
            $deed = Record::find($request->recordId);

            if ($deed->record_type_id != $deedType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$deed) {
                return response([
                    'status' => false,
                    'message' => "Tapu satış-kiralama işlem kaydı bulunamadı"
                ], 404);
            }

            if ($deed->approved_at != null || $deed->deleted_at != null) {
                return response([
                    'status' => false,
                    'message' => "Onaylanmış/Reddedilmiş bir kayıt onaylanamaz."
                ], 400);
            }
            $deed->approved_at = now();
            $recordUserId = $deed->userS->id;
            $deed->save();
            Notification::send(
                User::find($recordUserId),
                new SystemNotification(
                    __('Deed Sale-Rent Record Approved'),
                    __('Your deed sale-rent record has been approved by the office manager.'),
                    'platform.deed'
                )
            );
            return response([
                'status' => true,
                'message' => "Tapu satış-kiralama işlemi kaydı onaylandı",
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => __('Something went wrong'),
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request)
    {
        try {

            $deedType = RecordType::where('name', 'Tapu Satış-Kiralama İşlemleri')->first()->id;
            $deed = Record::find($request->recordId);

            if ($deed->record_type_id != $deedType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$deed) {
                return response([
                    'status' => false,
                    'message' => "Tapu satış-kiralama işlem kaydı bulunamadı"
                ], 404);
            }

            if ($deed->approved_at != null || $deed->deleted_at != null) {
                return response([
                    'status' => false,
                    'message' => "Onaylanmış/Reddedilmiş bir kayıt reddedilemez."
                ], 400);
            }
            $recordUserId = $deed->userS->id;
            $deed->delete();
            Notification::send(
                User::find($recordUserId),
                new SystemNotification(
                    __('Deed Sale-Rent Record Rejected'),
                    __('Your deed sale-rent record has been rejected by the office manager.'),
                    'platform.deed'
                )
            );
            return response([
                'status' => true,
                'message' => "Tapu satış-kiralama işlemi kaydı reddedildi",
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => __('Something went wrong'),
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
