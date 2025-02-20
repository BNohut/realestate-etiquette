<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Setting;
use App\Models\State;

class SettingsController
{
    public function config()
    {
        try {
            $setting = Setting::first()->config;

            if (!$setting) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }
            return response([
                'status' => true,
                'message' => "Veri aktarımı başarılı",
                'data' => Setting::first()->config
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function provinces()
    {
        try {
            $provinces = Province::all()->toArray();
            $states = State::all()->toArray();

            return response([
                'status' => true,
                'message' => "Türkiye Cumhuriyeti vilayet, kaza ve mahalle bilgilerinin aktarımı başarılı",
                'data' => ['provinces' => $provinces, 'states' => $states]
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
