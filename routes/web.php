<?php

use App\Http\Controllers\UserController;
use App\Models\Contact;
use App\Models\Portfolio;
use App\Models\Province;
use App\Models\Record;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('app');
// });
Route::get('/magic/{portfolio}', function ($portfolio) {
    $portfolio = Portfolio::firstWhere('slug', $portfolio);
    $records = Record::where('portfolio_id', $portfolio->id)
        ->where(function ($query) {
            $query->where('record_type_id', '<>', 6) // record_type_id 6 olmayanları al
                ->orWhere(function ($subQuery) {
                    $subQuery->where('record_type_id', 6)
                        ->whereNotNull('approved_at'); // record_type_id 6 olanların approved_at dolu olanları al
                });
        })
        ->get();
    // Record::where('portfolio_id', $portfolio->id)->get();
    return view('Magic/MagicLinkMain', ['portfolio' => $portfolio, 'records' => $records]);
})->name('magic');

Route::get('/magic/{portfolio}/record-detail/{recordId}', function ($portfolio, $recordId = null) {
    $record = Record::find($recordId);
    $portfolio = Portfolio::firstWhere('slug', $portfolio);
    // dd($record);
    if (!$record) {
        return redirect()->route('magic', ['portfolio' => $portfolio->slug]);
    }
    return view('Magic/MagicDetail', ['record' => $record]);
})->name('magic.detail');

Route::get('/', function () {
    return redirect('/admin/main');
});

Route::get('/sign-up', function () {
    return view('SignUp');
});

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('user.logout');

Route::post('/register', [UserController::class, 'register'])->name('user.register');

Route::get('/user/verify/{token}', [UserController::class, 'verify'])->name('verify.token');

Route::get('/provinces', function () {
    $provinces = Province::get(['id', 'name', 'plate_number']);
    if (!$provinces) {
        return response([
            'status' => 'error',
            'message' => 'Provinces not found'
        ], 404);
    }
    return response([
        'status' => 'success',
        'message' => 'Provinces found',
        'data' => $provinces,
    ], 200);
});

Route::get('/states/{provinceId}', function ($provinceId) {
    $states = State::where('province_id', $provinceId)->get(['id', 'name']);
    if (!$states) {
        return response([
            'status' => 'error',
            'message' => 'States not found'
        ], 404);
    }
    return response([
        'status' => 'success',
        'message' => 'States found',
        'data' => $states,
    ], 200);
});
