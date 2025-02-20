<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController
{
    public function list()
    {
        try {
            $notifications = DatabaseNotification::where('notifiable_id', auth()->user()->id)->get();
            if (count($notifications) == 0)
                return response([
                    'status' => false,
                    'message' => "Bildiriminiz bulunmamaktadır",
                ], 404);
            return response([
                'status' => true,
                'message' => "Bildirimler başarıyla listelendi",
                'data' => $notifications
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function markAsRead(Request $request)
    {
        try {
            $notificationIds = json_decode($request->notificationIds);

            if (!$notificationIds || !is_array($notificationIds)) {
                return response([
                    'status' => false,
                    'message' => "Bildirim idleri array olmalıdır"
                ], 400);
            }
            foreach ($notificationIds as $notificationId) {
                $notification = DatabaseNotification::find($notificationId);
                if (!$notification) {
                    return response([
                        'status' => false,
                        'message' => "Bildirim bulunamadı: $notificationId"
                    ], 404);
                }
                $notification->markAsRead();
            }
            return response([
                'status' => true,
                'message' => "Bildirimler başarıyla okundu",
                'data' => $notification
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }
    public function delete(Request $request)
    {
        try {
            $notificationIds = json_decode($request->notificationIds);

            if (!$notificationIds) {
                return response([
                    'status' => false,
                    'message' => "Hatalı istek"
                ], 400);
            }
            foreach ($notificationIds as $notificationId) {
                $notification = DatabaseNotification::find($notificationId);
                if (!$notification) {
                    return response([
                        'status' => false,
                        'message' => "Bildirim bulunamadı: $notificationId"
                    ], 404);
                }
                $notification->delete();
            }
            return response([
                'status' => true,
                'message' => "Bildirim başarıyla silindi",
                'data' => $notification
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
