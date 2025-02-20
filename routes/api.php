<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtVerify;
use App\Http\Middleware\RequestMethodMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post("/login", [UserController::class, "login"]);


Route::middleware([RequestMethodMiddleware::class, JwtVerify::class])->group(function () {
    Route::any(
        '/{controller}/{action}',
        function ($controller, $action, Request $request) {
            $controllerClass = "App\\Http\\Controllers\\" . ucfirst($controller) . "Controller";
            return (new $controllerClass)->{$action}($request);
        }
    );
});
