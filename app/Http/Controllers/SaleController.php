<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\RecordType;
use Illuminate\Http\Request;

class SaleController
{
    public function list(Request $request)
    {
        try {
            $page = $request->page;
            $itemsPerPage = $request->itemsPerPage;

            $saleType = RecordType::where('name', 'Satış Kapama')->first()->id;
            if (authUserInRole(['super-yonetici', 'yonetici'])) {
                $sales = Record::where('record_type_id', $saleType)->paginate($itemsPerPage, ['*'], 'page', $page);
            } elseif (authUserInRole('ofis-yoneticisi')) {
                $sales = Record::join('users', 'records.user_id', '=', 'users.id')
                    ->where([['records.record_type_id', $saleType], ['users.office_id', auth()->user()->office_id]])->select('records.*')->paginate($itemsPerPage, ['*'], 'page', $page);
            } elseif (authUserInRole(['bireysel-danisman', 'ofis-danismani'])) {
                $sales = Record::where([['record_type_id', $saleType], ['user_id', auth()->user()->id]])->paginate($itemsPerPage, ['*'], 'page', $page);
            }

            if ($sales->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }

            $sales->each(function ($record) {
                $record->append('portfolio_attachments');
            });

            return response([
                'status' => true,
                'config' => ['page' => $page, 'itemsPerPage' => $itemsPerPage],
                'message' => "Satış kapama verileri aktarımı başarılı.",
                'data' => $sales
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
            $saleType = RecordType::where('name', 'Satış Kapama')->first()->id;
            $sale = Record::find($request->recordId)->append('portfolio_attachments');

            if ($sale->record_type_id != $saleType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$sale) {
                return response([
                    'status' => false,
                    'message' => "Satış kapama kaydı bulunamadı"
                ], 404);
            }

            return response([
                'status' => true,
                'message' => "Satış kapama kaydı bilgisi aktarımı başarılı",
                'data' => $sale
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
            $saleType = RecordType::where('name', 'Satış Kapama')->first()->id;
            $sale = new Record();
            $sale->fill($request->all());
            $sale->record_type_id = $saleType;
            $sale->save();

            $newSale = Record::find($sale->id)->append('portfolio_attachments');
            return response([
                'status' => true,
                'message' => "Satış kapama kaydı başarıyla oluşturuldu.",
                'data' => $newSale
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
            $saleType = RecordType::where('name', 'Satış Kapama')->first()->id;
            $sale = Record::find((int) $request->recordId)->append('portfolio_attachments');

            if ($sale->record_type_id != $saleType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$sale) {
                return response([
                    'status' => false,
                    'message' => "Satış kapama kaydı bulunamadı"
                ], 404);
            }

            $sale
                ->fill($request->all())
                ->save();


            return response([
                'status' => true,
                'message' => "Sale closing record updated successfully.",
                'data' => $sale
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
            $saleType = RecordType::where('name', 'Satış Kapama')->first()->id;
            $sale = Record::find($request->recordId);

            if ($sale->record_type_id != $saleType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$sale) {
                return response([
                    'status' => false,
                    'message' => "Satış kapama kaydı bulunamadı"
                ], 404);
            }

            $sale->delete();

            return response([
                'status' => true,
                'message' => 'Satış kapama kaydı başarıyla silindi'
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
