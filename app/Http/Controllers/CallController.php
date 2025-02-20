<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\RecordType;
use Illuminate\Http\Request;

class CallController
{
    public function list(Request $request)
    {
        try {
            $page = $request->page;
            $itemsPerPage = $request->itemsPerPage;

            $callTypeId = RecordType::where('name', 'Çağrı')->first()->id;
            if ($request->portfolioId) {
                $calls = Record::where([['record_type_id', $callTypeId], ['portfolio_id', $request->portfolioId]])->paginate($itemsPerPage, ['*'], 'page', $page);
            } else {
                if (authUserInRole(['super-yonetici', 'yonetici'])) {
                    $calls = Record::where('record_type_id', $callTypeId)->paginate($itemsPerPage, ['*'], 'page', $page);
                } elseif (authUserInRole('ofis-yoneticisi')) {
                    $calls = Record::join('users', 'records.user_id', '=', 'users.id')
                        ->where([['records.record_type_id', $callTypeId], ['users.office_id', auth()->user()->office_id]])->select('records.*')->paginate($itemsPerPage, ['*'], 'page', $page);
                } elseif (authUserInRole(['bireysel-danisman', 'ofis-danismani'])) {
                    $calls = Record::where([['record_type_id', $callTypeId], ['user_id', auth()->user()->id]])->paginate($itemsPerPage, ['*'], 'page', $page);
                }
            }
            $calls->each(function ($call) {
                $call->append('portfolio_list_price');
            });

            if ($calls->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }

            return response([
                'status' => true,
                'config' => ['page' => $page, 'itemsPerPage' => $itemsPerPage],
                'message' => "Çağrı kayıtları verileri aktarımı başarılı.",
                'data' => $calls
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
            $callType = RecordType::where('name', 'Çağrı')->first()->id;
            $call = Record::find((int) $request->recordId)->append('portfolio_list_price');

            if ($call->record_type_id != $callType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$call) {
                return response([
                    'status' => false,
                    'message' => "Çağrı kaydı bulunamadı"
                ], 404);
            }

            return response([
                'status' => true,
                'message' => "Çağrı kaydı bilgisi aktarımı başarılı",
                'data' => $call
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
            $callType = RecordType::where('name', 'Çağrı')->first()->id;
            $call = new Record();
            $call->fill($request->all());
            $call->record_type_id = $callType;
            $call->save();

            $newCall = Record::find($call->id)->append('portfolio_list_price');

            return response([
                'status' => true,
                'message' => "Çağrı kaydı başarıyla oluşturuldu.",
                'data' => $newCall
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
            $callType = RecordType::where('name', 'Çağrı')->first()->id;
            $call = Record::find((int) $request->recordId)->append('portfolio_list_price');

            if ($call->record_type_id != $callType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$call) {
                return response([
                    'status' => false,
                    'message' => "Çağrı kaydı bulunamadı"
                ], 404);
            }

            $call
                ->fill($request->all())
                ->save();

            $call->append('portfolio_list_price');

            return response([
                'status' => true,
                'message' => "Call record updated successfully.",
                'data' => $call
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
            $callType = RecordType::where('name', 'Çağrı')->first()->id;
            $call = Record::find($request->recordId);

            if ($call->record_type_id != $callType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$call) {
                return response([
                    'status' => false,
                    'message' => "Çağrı kaydı bulunamadı"
                ], 404);
            }

            $call->delete();

            return response([
                'status' => true,
                'message' => 'Çağrı kaydı başarıyla silindi'
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
