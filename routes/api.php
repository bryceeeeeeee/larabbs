<?php

use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\VerificationCodesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->name('api.v1.')->group(function () {
    // 短信验证码
    Route::post('verification/codes', [VerificationCodesController::class, 'store'])->name('verification_codes.store');
    // 用户注册
    Route::post('users', [UsersController::class, 'store'])->name('users.store');

    Route::get('version', function () {
        abort(403, 'test');
        return 'this is version v1';
    })->name('version');
});

Route::prefix('v2')->name('api.v2.')->group(function () {
    Route::get('version', function () {
        return 'this is version v2';
    })->name('version');
});
