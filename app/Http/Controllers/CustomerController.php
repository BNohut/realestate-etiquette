<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\RecordType;
use Illuminate\Http\Request;

class CustomerController
{
    public function list(Request $request)
    {
        try {
            $page = $request->page;
            $itemsPerPage = $request->itemsPerPage;

            $customerType = RecordType::where('name', 'Alıcı Müşteri')->first()->id;
            if (authUserInRole(['super-yonetici', 'yonetici'])) {
                $customers = Record::where('record_type_id', $customerType)->paginate($itemsPerPage, ['*'], 'page', $page);
            } elseif (authUserInRole('ofis-yoneticisi')) {
                $customers = Record::join('users', 'records.user_id', '=', 'users.id')
                    ->where([['records.record_type_id', $customerType], ['users.office_id', auth()->user()->office_id]])->select('records.*')->paginate($itemsPerPage, ['*'], 'page', $page);
            } elseif (authUserInRole(['bireysel-danisman', 'ofis-danismani'])) {
                $customers = Record::where([['record_type_id', $customerType], ['user_id', auth()->user()->id]])->paginate($itemsPerPage, ['*'], 'page', $page);
            }
            if ($customers->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }

            return response([
                'status' => true,
                'config' => ['page' => $page, 'itemsPerPage' => $itemsPerPage],
                'message' => "Alıcı müşteri kayıtları verileri aktarımı başarılı.",
                'data' => $customers
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
            $customerType = RecordType::where('name', 'Alıcı Müşteri')->first()->id;
            $customer = Record::find($request->recordId);

            if ($customer->record_type_id != $customerType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$customer) {
                return response([
                    'status' => false,
                    'message' => "Alıcı müşteri kaydı bulunamadı"
                ], 404);
            }

            return response([
                'status' => true,
                'message' => "Alıcı müşteri kaydı bilgisi aktarımı başarılı",
                'data' => $customer
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
            $customerType = RecordType::where('name', 'Alıcı Müşteri')->first()->id;
            $customer = new Record();
            $customer->fill($request->all());
            $customer->record_type_id = $customerType;
            $customer->save();

            return response([
                'status' => true,
                'message' => "Alıcı müşteri kaydı başarıyla oluşturuldu.",
                'data' => $customer
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
            $customerType = RecordType::where('name', 'Alıcı Müşteri')->first()->id;
            $customer = Record::find((int) $request->recordId);

            if ($customer->record_type_id != $customerType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$customer) {
                return response([
                    'status' => false,
                    'message' => "Alıcı müşteri kaydı bulunamadı"
                ], 404);
            }

            $customer
                ->fill($request->all())
                ->save();


            return response([
                'status' => true,
                'message' => "Alıcı müşteri kaydı başarıyla güncellendi",
                'data' => $customer
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
            $customerType = RecordType::where('name', 'Alıcı Müşteri')->first()->id;
            $customer = Record::find($request->recordId);

            if ($customer->record_type_id != $customerType) {
                return response([
                    'status' => false,
                    'message' => "Hatalı parametre"
                ], 400);
            }

            if (!$customer) {
                return response([
                    'status' => false,
                    'message' => "Alıcı müşteri kaydı bulunamadı"
                ], 404);
            }

            $customer->delete();

            return response([
                'status' => true,
                'message' => 'Alıcı müşteri kaydı başarıyla silindi'
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
