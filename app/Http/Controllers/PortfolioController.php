<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Orchid\Attachment\File;

class PortfolioController
{
    public function all(Request $request)
    {
        try {
            if ($request->consultantId) {
                // if (authUserInRole(['super-yonetici', 'yonetici'])) {
                //     $query = Portfolio::orderBy('title', 'asc')->select('portfolio.id', 'portfolio.title');
                // }
                // if (authUserInRole(['ofis-yoneticisi', 'ofis-asistani', 'ofis-danismani'])) {
                //     $query = Portfolio::join('users', 'users.id', '=', 'portfolios.user_id')
                //     ->where('users.office_id', auth()->user()->office_id)
                //         ->orderBy('portfolio.title', 'asc')
                //         ->select('portfolios.id', 'portfolios.title');
                // }
                $portfolios = Portfolio::where('portfolios.user_id', $request->consultantId)->orderBy('portfolios.title', 'asc')
                    ->select('portfolios.id', 'portfolios.title')->get();
            }
            if (authUserInRole('bireysel-danisman')) {
                $portfolios = Portfolio::where('user_id', auth()->user()->id)
                    ->orderBy('portfolios.title', 'asc')
                    ->select('portfolios.id', 'portfolios.title')->get();
            }

            if ($portfolios->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }

            return response([
                'status' => true,
                'message' => "Portföy verileri aktarımı başarılı.",
                'data' => $portfolios
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong during listing portfolios.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $page = $request->page;
            $itemsPerPage = $request->itemsPerPage;

            $portfolios = Portfolio::paginate($itemsPerPage, ['*'], 'page', $page);

            foreach ($portfolios as $key => $portfolio) {
                $portfolioAttachments = $portfolio->attachment()->get()->toArray();
                $result = array_map(function ($attachment) {
                    return [
                        'mime' => $attachment['mime'],
                        'extension' => $attachment['extension'],
                        'url' => $attachment['url'],
                        'name' => $attachment['name'],
                        'id' => $attachment['id'],
                    ];
                }, $portfolioAttachments);

                $portfolio->attachments = $result;
            }
            if ($portfolios->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }

            // var_dump($portfolios);
            return response([
                'status' => true,
                'config' => ['page' => $page, 'itemsPerPage' => $itemsPerPage],
                'message' => "Portföy verileri aktarımı başarılı.",
                'data' => $portfolios
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong during listing portfolios.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function detail(Request $request)
    {
        try {
            $portfolio = Portfolio::find($request->portfolioId);

            $portfolioAttachments = $portfolio->attachment()->get()->toArray();
            $result = array_map(function ($attachment) {
                return [
                    'mime' => $attachment['mime'],
                    'extension' => $attachment['extension'],
                    'url' => $attachment['url'],
                    'name' => $attachment['name'],
                    'id' => $attachment['id'],
                ];
            }, $portfolioAttachments);
            $portfolio->attachments = $result;

            if (!$portfolio) {
                return response([
                    'status' => false,
                    'message' => "Portföy bulunamadı"
                ], 404);
            }

            return response([
                'status' => true,
                'message' => "Portföy bilgisi aktarımı başarılı",
                'data' => $portfolio
            ], 200);
        } catch (\Exception $error) {

            return response([
                'status' => false,
                'message' => "Something went wrong during get portfolio detail.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function create(Request $request)
    {
        // image_count parameter will be in $request
        try {
            //Create a new Portfolio
            $newPortfolio = new Portfolio();
            //Fill & Save it by request
            $newPortfolio
                ->fill($request->toArray())
                ->save();
            //Catch image_count parameter 
            $imageCount = $request->image_count;

            //If there is/are image(s)
            if ($imageCount > 0) {
                $attachmentIds = [];
                $portfolioRequest = $request->all();

                // Loop trough images & Upload via HTTP method 
                for ($i = 1; $i <= $imageCount; $i++) {
                    //Create File Object
                    $file = new File($request->file('image_' . $i));
                    //Load File
                    $attachment = $file->allowDuplicates()->load();
                    //Put it to Request Object
                    $portfolioRequest['image_' . $i] = $attachment;
                    //Replace Request Object
                    $request->replace($portfolioRequest);
                    //Sync image to DB using by Orchid Method
                    $newPortfolio->attachment()->syncWithoutDetaching(
                        $request->input('image_' . $i, [])
                    );
                    //Push attachment ID to attachmentIds array for Model Saving
                    array_push($attachmentIds, $attachment->id);
                }
                //Set the attachmentIds as model 'image' field 
                $newPortfolio
                    ->fill(['images' => $attachmentIds])
                    ->save();


                $portfolioAttachments = $newPortfolio->attachment()->get()->toArray();
                $result = array_map(function ($attachment) {
                    return [
                        'mime' => $attachment['mime'],
                        'extension' => $attachment['extension'],
                        'url' => $attachment['url'],
                        'name' => $attachment['name'],
                        'id' => $attachment['id'],
                    ];
                }, $portfolioAttachments);
                $newPortfolio->attachments = $result;

                return response([
                    'status' => true,
                    'message' => "Portfolio created with images.",
                    'data' => $newPortfolio
                ], 200);
            } else {

                return response([
                    'status' => true,
                    'message' => "Portfolio created without images.",
                    'data' => $newPortfolio
                ], 200);
            }
        } catch (\Exception $error) {

            return response([
                'status' => false,
                'message' => "Something went wrong during creating portfolio.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $portfolio = Portfolio::find((int) $request->portfolioId);
            $imageCount = $request->image_count;
            if (!$portfolio) {
                return response([
                    'status' => false,
                    'message' => "Portföy bulunamadı"
                ], 404);
            }

            if ($imageCount > 0) {

                if ($portfolio->attachment()->count() > 0) {
                    $portfolio->attachment->each->delete();
                }
                $attachmentIds = [];
                $portfolioRequest = $request->all();

                // Loop trough images & Upload via HTTP method 
                for ($i = 1; $i <= $imageCount; $i++) {
                    //Create File Object
                    $file = new File($request->file('image_' . $i));
                    //Load File
                    $attachment = $file->allowDuplicates()->load();
                    //Put it to Request Object
                    $portfolioRequest['image_' . $i] = $attachment;
                    //Replace Request Object
                    $request->replace($portfolioRequest);
                    //Sync image to DB using by Orchid Method
                    $portfolio->attachment()->syncWithoutDetaching(
                        $request->input('image_' . $i, [])
                    );
                    //Push attachment ID to attachmentIds array for Model Saving
                    array_push($attachmentIds, $attachment->id);
                }
                //Set the attachmentIds as model 'image' field 
                $portfolio
                    ->fill($request->all())
                    ->fill(['images' => $attachmentIds])
                    ->save();


                $portfolio->load('attachment');

                return response([
                    'status' => true,
                    'message' => "Portfolio updated with new images",
                    'data' => $portfolio
                ], 200);
            } else {
                $portfolio
                    ->fill($request->all())
                    ->save();

                $portfolio->load('attachment');

                return response([
                    'status' => true,
                    'message' => "Portfolio updated without image.",
                    'data' => $portfolio
                ], 200);
            }
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong during saving portfolio.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $portfolio = Portfolio::find($request->portfolioId);

            if (!$portfolio) {
                return response([
                    'status' => false,
                    'message' => "Portföy bulunamadı"
                ], 404);
            }
            $portfolio->attachment->each->delete();
            $portfolio->delete();

            return response([
                'status' => true,
                'message' => 'Portföy başarıyla silindi'
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
