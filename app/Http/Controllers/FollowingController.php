<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class FollowingController
{
    public function follow(Request $request)
    {
        try {
            $from = auth()->user()->id;
            $to = $request->userId;;
            $newFollowRecord = Follower::create([
                'from' => $from,
                'to' => $to,
                'approved' => null,
            ]);

            if (!$newFollowRecord) {
                return response([
                    'status' => false,
                    'message' => "Something went wrong"
                ], 500);
            }

            //Send Notification to Following Consultant
            Notification::send(
                User::find($to),
                new SystemNotification(
                    "Takip isteği alındı!",
                    $newFollowRecord->from_name . " size takip isteği gönderdi.",
                    'platform.consultant'
                )
            );

            return response([
                'status' => true,
                'message' => "Takip isteği gönderildi."
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function cancel(Request $request)
    {
        try {
            $from = auth()->user()->id;
            $to = $request->userId;
            $followRecord = Follower::where('from', $from)
                ->where('to', $to)
                ->whereNull('approved')
                ->first();

            if (!$followRecord) {
                return response([
                    'status' => false,
                    'message' => "Takip isteği bulunamadı"
                ], 500);
            }

            $followRecord->delete();

            return response([
                'status' => true,
                'message' => "Takip isteği iptal edildi."
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request)
    {
        try {
            $from = $request->userId;
            $to = auth()->user()->id;
            $followRecord = Follower::where('from', $from)
                ->where('to', $to)
                ->first();
            if (!$followRecord) {
                return response([
                    'status' => false,
                    'message' => "Takip isteği bulunamadı"
                ], 500);
            }
            $followRecord->approved = now();
            $followRecord->save();

            //Send Notification to Follower Consultant
            Notification::send(
                User::find($from),
                new SystemNotification(
                    "Takip isteği onaylandı!",
                    $followRecord->to_name . " takip isteğinizi onayladı.",
                    'platform.consultant'
                )
            );
            return response([
                'status' => true,
                'message' => "Takip isteği onaylandı."
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request)
    {
        try {
            $from = $request->userId;
            $to = auth()->user()->id;
            $followRecord = Follower::where('from', $from)
                ->where('to', $to)
                ->first();
            if (!$followRecord) {
                return response([
                    'status' => false,
                    'message' => "Takip isteği bulunamadı"
                ], 500);
            }
            $followRecord->delete();
            return response([
                'status' => true,
                'message' => "Takip isteği reddedildi."
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function followBack(Request $request)
    {
        try {
            $from = auth()->user()->id;
            $to = $request->userId;
            $newFollowRecord = Follower::create([
                'from' => $from,
                'to' => $to,
            ]);

            if (!$newFollowRecord) {
                return response([
                    'status' => false,
                    'message' => "Something went wrong"
                ], 500);
            }

            //Send Notification to Following Consultant
            Notification::send(
                User::find($to),
                new SystemNotification(
                    "Takip isteği alındı!",
                    "Takip ettiğiniz " . $newFollowRecord->from_name . " de size takip isteği gönderdi.",
                    'platform.consultant'
                )
            );

            return response([
                'status' => true,
                'message' => "Takip isteği gönderildi."
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function unfollow(Request $request)
    {
        try {
            $from = auth()->user()->id;
            $to = $request->userId;
            $followRecord = Follower::where('from', $from)
                ->where('to', $to)
                ->first();
            if (!$followRecord) {
                return response([
                    'status' => false,
                    'message' => "Something went wrong"
                ], 500);
            }
            $followRecord->delete();
            return response([
                'status' => true,
                'message' => "Takipten çıkıldı."
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function followStatus(Request $request)
    {
        try {
            $from = auth()->user()->id;
            $to = $request->userId;
            $followRecord = Follower::where('from', $from)
                ->where('to', $to)
                ->first();

            $followBackRecord = Follower::where('to', $from)
                ->where('from', $to)
                ->first();

            return response([
                'status' => true,
                'message' => "Kullanıcıların takip bilgileri aktarıldı",
                'followRecord' => $followRecord,
                'followBackRecord' => $followBackRecord,
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
