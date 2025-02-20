<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\RecordType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingController
{
    public function list(Request $request)
    {
        try {
            $page = $request->page;
            $itemsPerPage = $request->itemsPerPage;

            $marketingType = RecordType::where('name', 'Pazarlama')->first()->id;
            if (authUserInRole(['super-yonetici', 'yonetici'])) {
                $marketings = Record::where('record_type_id', $marketingType)->paginate($itemsPerPage, ['*'], 'page', $page);
            } elseif (authUserInRole('ofis-yoneticisi')) {
                $marketings = Record::join('users', 'records.user_id', '=', 'users.id')
                    ->where([['records.record_type_id', $marketingType], ['users.office_id', auth()->user()->office_id]])->select('records.*')->paginate($itemsPerPage, ['*'], 'page', $page);
            } elseif (authUserInRole(['bireysel-danisman', 'ofis-danismani'])) {
                $marketings = Record::where([['record_type_id', $marketingType], ['user_id', auth()->user()->id]])->paginate($itemsPerPage, ['*'], 'page', $page);
            }

            if ($marketings->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }

            $marketings->each(function ($record) {
                $record->append('portfolio_attachments');
            });


            return response([
                'status' => true,
                'config' => ['page' => $page, 'itemsPerPage' => $itemsPerPage],
                'message' => "Pazarlama kayıtları verileri aktarımı başarılı.",
                'data' => $marketings
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
            $marketingType = RecordType::where('name', 'Pazarlama')->first()->id;
            $marketing = Record::find($request->recordId)->append('portfolio_attachments');

            if ($marketing->record_type_id != $marketingType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$marketing) {
                return response([
                    'status' => false,
                    'message' => "Pazarlama kaydı bulunamadı"
                ], 404);
            }

            return response([
                'status' => true,
                'message' => "Pazarlama kaydı bilgisi aktarımı başarılı",
                'data' => $marketing
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
            $marketingType = RecordType::where('name', 'Pazarlama')->first()->id;
            $marketing = new Record();
            $marketing->fill($request->all());
            $marketing->record_type_id = $marketingType;
            $marketing->save();

            $newMarketing = Record::find($marketing->id)->append('portfolio_attachments');

            return response([
                'status' => true,
                'message' => "Pazarlama kaydı başarıyla oluşturuldu.",
                'data' => $newMarketing
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
            $marketingType = RecordType::where('name', 'Pazarlama')->first()->id;
            $marketing = Record::find((int) $request->recordId)->append('portfolio_attachments');

            if ($marketing->record_type_id != $marketingType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$marketing) {
                return response([
                    'status' => false,
                    'message' => "Pazarlama kaydı bulunamadı"
                ], 404);
            }

            $marketing
                ->fill($request->all())
                ->save();


            return response([
                'status' => true,
                'message' => "Pazarlama kaydı başarıyla güncellendi",
                'data' => $marketing
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
            $marketingType = RecordType::where('name', 'Pazarlama')->first()->id;
            $marketing = Record::find($request->recordId);

            if ($marketing->record_type_id != $marketingType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$marketing) {
                return response([
                    'status' => false,
                    'message' => "Pazarlama kaydı bulunamadı"
                ], 404);
            }

            $marketing->delete();

            return response([
                'status' => true,
                'message' => 'Pazarlama kaydı başarıyla silindi'
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
