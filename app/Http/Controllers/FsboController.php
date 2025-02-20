<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\RecordType;
use Illuminate\Http\Request;

class FsboController
{
    public function list(Request $request)
    {
        try {
            $page = $request->page;
            $itemsPerPage = $request->itemsPerPage;

            $fsboTypeId = RecordType::where('name', 'F.S.B.O.')->first()->id;
            if (authUserInRole(['super-yonetici', 'yonetici'])) {
                $fsbos = Record::where('record_type_id', $fsboTypeId)->paginate($itemsPerPage, ['*'], 'page', $page);
            } elseif (authUserInRole('ofis-yoneticisi')) {
                $fsbos = Record::join('users', 'records.user_id', '=', 'users.id')
                    ->where([['records.record_type_id', $fsboTypeId], ['users.office_id', auth()->user()->office_id]])->select('records.*')->paginate($itemsPerPage, ['*'], 'page', $page);
            } elseif (authUserInRole(['bireysel-danisman', 'ofis-danismani'])) {
                $fsbos = Record::where([['record_type_id', $fsboTypeId], ['user_id', auth()->user()->id]])->paginate($itemsPerPage, ['*'], 'page', $page);
            }
            if ($fsbos->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }

            return response([
                'status' => true,
                'config' => ['page' => $page, 'itemsPerPage' => $itemsPerPage],
                'message' => "FSBO kayıtları verileri aktarımı başarılı.",
                'data' => $fsbos
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
            $fsboType = RecordType::where('name', 'F.S.B.O.')->first()->id;
            $fsbo = Record::find($request->recordId);

            if ($fsbo->record_type_id != $fsboType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$fsbo) {
                return response([
                    'status' => false,
                    'message' => "FSBO kaydı bulunamadı"
                ], 404);
            }

            return response([
                'status' => true,
                'message' => "FSBO kaydı bilgisi aktarımı başarılı",
                'data' => $fsbo
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
            $fsboType = RecordType::where('name', 'F.S.B.O.')->first()->id;
            $fsbo = new Record();
            $fsbo->fill($request->all());
            $fsbo->record_type_id = $fsboType;
            $fsbo->save();

            return response([
                'status' => true,
                'message' => "FSBO kaydı başarıyla oluşturuldu.",
                'data' => $fsbo
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
            $fsboType = RecordType::where('name', 'F.S.B.O.')->first()->id;
            $fsbo = Record::find((int) $request->recordId);

            if ($fsbo->record_type_id != $fsboType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$fsbo) {
                return response([
                    'status' => false,
                    'message' => "FSBO kaydı bulunamadı"
                ], 404);
            }

            $fsbo
                ->fill($request->all())
                ->save();


            return response([
                'status' => true,
                'message' => "FSBO kaydı başarıyla güncellendi",
                'data' => $fsbo
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
            $fsboType = RecordType::where('name', 'F.S.B.O.')->first()->id;
            $fsbo = Record::find($request->recordId);

            if ($fsbo->record_type_id != $fsboType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$fsbo) {
                return response([
                    'status' => false,
                    'message' => "FSBO kaydı bulunamadı"
                ], 404);
            }

            $fsbo->delete();

            return response([
                'status' => true,
                'message' => 'FSBO kaydı başarıyla silindi'
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
