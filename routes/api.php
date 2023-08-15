<?php

use App\Http\Controllers\Api\CaptchasController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\VerificationCodesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthorizationsController;

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
    Route::post('verification/codes', [VerificationCodesController::class, 'store'])
        ->name('verification_codes.store')
        ->middleware('throttle:' . config('api.rate_limits.sign'));
    // 用户注册
    Route::post('users', [UsersController::class, 'store'])->name('users.store');
    // 第三方登录-微信
    Route::post('socials/{social_type}/authorizations', [AuthorizationsController::class, 'socialStore'])
        ->where('social_type', 'wechat|weibo')
        ->name('social.authorizations.store');

    // 登录
    Route::post('authorizations', [AuthorizationsController::class, 'store'])
        ->name('authorizations.store');
    // 刷新token
    Route::put('authorizations/current', [AuthorizationsController::class, 'update'])->name('authorizations.update');
    // 删除token
    Route::delete('authorizations/current', [AuthorizationsController::class, 'delete'])->name('authorizations.destory');


    Route::middleware('throttle:' . config('api.rate_limits.access'))
        ->group(function () {
            // 图片验证码
            Route::post('captchas', [CaptchasController::class, 'store'])->name('captchas.store');
            // 游客可以访问的接口
            // 某个用户信息
            Route::get('users/{user}', [UsersController::class, 'show'])
                ->name('users.show');

            // 登录后可以访问的接口
            Route::middleware('auth:api')->group(function () {
                // 当前登录的用户信息
                Route::get('user', [UsersController::class, 'me'])->name('user.show');
            });
        });

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
