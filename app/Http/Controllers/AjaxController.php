<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Record;
use Illuminate\Http\Request;


class AjaxController
{
    public function deleteImage(Request $request)
    {
        try {
            $portfolioId = $request->portfolioId;
            $portfolio = Portfolio::where('id', $portfolioId)->first();
            if (!$portfolio) {
                return response([
                    'status' => false,
                    'message' => "Portföy bulunamadı"
                ], 404);
            }
            $portfolio->load('attachment');
            $portfolio->attachment[$request->index]->delete();

            $portfolioImages = json_decode($portfolio->images);

            array_splice($portfolioImages, $request->index, 1);
            $portfolio->images = json_encode($portfolioImages);
            $portfolio->save();

            return response([
                'status' => true,
                'message' => "Resim silindi"
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function deletePortfolio(Request $request)
    {
        try {
            $portfolioId = $request->portfolioId;
            $portfolio = Portfolio::where('id', $portfolioId)->first();
            if (!$portfolio) {
                return response([
                    'status' => false,
                    'message' => "Portföy bulunamadı"
                ], 404);
            }
            $portfolio->load('attachment');
            if ($portfolio->attachment) {
                $portfolio->attachment->each->delete();
            }
            $portfolio->delete();

            return response([
                'status' => true,
                'message' => 'Portföy Silindi'
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'status' => false,
                'message' => 'Bir sebeple silme işlemi gerçekleşmedi',
                'error' => $error->getMessage()
            ]);
        }
    }

    public function deleteRecord(Request $request)
    {
        try {
            $recordId = $request->recordId;
            $record = Record::where('id', $recordId)->first();
            if (!$record) {
                return response([
                    'status' => false,
                    'message' => "Kayıt bulunamadı",
                ], 404);
            }

            $record->delete();

            return response([
                'status' => true,
                'message' => 'Kayıt Silindi'
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => 'Bir sebeple silme işlemi gerçekleşmedi',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
