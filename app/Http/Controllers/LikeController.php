<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;

class LikeController
{
    public function like(Request $request)
    {
        try {
            $record = Record::find($request->recordId);
            if (!$record) {
                return response([
                    'status' => false,
                    'message' => "Feed not found",
                ], 404);
            }
            if ($record->likes) {
                $decodedLikes = json_decode($record->likes);
                if (!in_array(auth()->user()->id, $decodedLikes)) {
                    $decodedLikes[] = auth()->user()->id;
                    $record->likes = json_encode($decodedLikes);
                    $record->save();
                } else {
                    return response([
                        'status' => false,
                        'message' => "Feed already liked",
                    ], 400);
                }
            } else {
                $record->likes = json_encode([auth()->user()->id]);
                $record->save();
            }
            return response([
                'status' => true,
                'message' => "Feed liked",
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function unlike(Request $request)
    {
        try {
            $record = Record::find($request->recordId);
            if (!$record) {
                return response([
                    'status' => false,
                    'message' => "Feed not found",
                ], 404);
            }
            if ($record->likes) {
                $decodedLikes = json_decode($record->likes, true);
                if (($key = array_search(auth()->user()->id, $decodedLikes)) !== false) {
                    unset($decodedLikes[$key]);
                    if (count($decodedLikes) > 0) {
                        $record->likes = json_encode($decodedLikes);
                    } else {
                        $record->likes = null;
                    }
                    $record->save();
                } else {
                    return response([
                        'status' => false,
                        'message' => "Feed not liked",
                    ], 400);
                }
            } else {
                return response([
                    'status' => false,
                    'message' => "This feed doesnt have any like",
                ], 400);
            }

            return response([
                'status' => true,
                'message' => "Feed unliked",
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
