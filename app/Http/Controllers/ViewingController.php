<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\RecordType;
use Illuminate\Http\Request;

class ViewingController
{
    public function list(Request $request)
    {
        try {
            $page = $request->page;
            $itemsPerPage = $request->itemsPerPage;

            $viewingType = RecordType::where('name', 'Yer Gösterme')->first()->id;
            if ($request->portfolioId) {
                $viewings = Record::where([['record_type_id', $viewingType], ['portfolio_id', $request->portfolioId]])->paginate($itemsPerPage, ['*'], 'page', $page);
            } else {
                if (authUserInRole(['super-yonetici', 'yonetici'])) {
                    $viewings = Record::where('record_type_id', $viewingType)->paginate($itemsPerPage, ['*'], 'page', $page);
                } elseif (authUserInRole('ofis-yoneticisi')) {
                    $viewings = Record::join('users', 'records.user_id', '=', 'users.id')
                        ->where([['records.record_type_id', $viewingType], ['users.office_id', auth()->user()->office_id]])->select('records.*')->paginate($itemsPerPage, ['*'], 'page', $page);
                } elseif (authUserInRole(['bireysel-danisman', 'ofis-danismani'])) {
                    $viewings = Record::where([['record_type_id', $viewingType], ['user_id', auth()->user()->id]])->paginate($itemsPerPage, ['*'], 'page', $page);
                }
            }

            if ($viewings->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }

            $viewings->each(function ($record) {
                $record->append(['portfolio_list_price', 'portfolio_attachments']);
            });

            return response([
                'status' => true,
                'config' => ['page' => $page, 'itemsPerPage' => $itemsPerPage],
                'message' => "Yer gösterme kayıtları verileri aktarımı başarılı.",
                'data' => $viewings
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
            $viewingType = RecordType::where('name', 'Yer Gösterme')->first()->id;
            $viewing = Record::find($request->recordId)->append(['portfolio_list_price', 'portfolio_attachments']);

            if ($viewing->record_type_id != $viewingType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$viewing) {
                return response([
                    'status' => false,
                    'message' => "Yer gösterme kaydı bulunamadı"
                ], 404);
            }

            return response([
                'status' => true,
                'message' => "Yer gösterme kaydı bilgisi aktarımı başarılı",
                'data' => $viewing
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
            $viewingType = RecordType::where('name', 'Yer Gösterme')->first()->id;
            $viewing = new Record();
            $viewing->fill($request->all());
            $viewing->record_type_id = $viewingType;
            $viewing->save();

            $newViewing = Record::find($viewing->id)->append(['portfolio_list_price', 'portfolio_attachments']);

            return response([
                'status' => true,
                'message' => "Yer gösterme kaydı başarıyla oluşturuldu.",
                'data' => $newViewing
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
            $viewingType = RecordType::where('name', 'Yer Gösterme')->first()->id;
            $viewing = Record::find((int) $request->recordId)->append(['portfolio_list_price', 'portfolio_attachments']);

            if ($viewing->record_type_id != $viewingType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$viewing) {
                return response([
                    'status' => false,
                    'message' => "Yer gösterme kaydı bulunamadı"
                ], 404);
            }

            $viewing
                ->fill($request->all())
                ->save();


            return response([
                'status' => true,
                'message' => "Viewing record updated successfully.",
                'data' => $viewing
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
            $viewingType = RecordType::where('name', 'Yer Gösterme')->first()->id;
            $viewing = Record::find($request->recordId);

            if ($viewing->record_type_id != $viewingType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$viewing) {
                return response([
                    'status' => false,
                    'message' => "Yer gösterme kaydı bulunamadı"
                ], 404);
            }

            $viewing->delete();

            return response([
                'status' => true,
                'message' => 'Yer gösterme kaydı başarıyla silindi'
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
