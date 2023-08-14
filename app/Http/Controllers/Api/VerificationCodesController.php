<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Api\VerificationCodeRequest;

class VerificationCodesController extends Controller
{
    //
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $phone = $request->phone;

        if (! app()->environment('production')) {
            $code = '1234';
        } else {

            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

            try {
                $res = $easySms->send($phone, [
                    'template' => config('easysms.gateways.aliyun.templates.register'),
                    'data' => [
                        'code' => $code
                    ]
                ]);
            } catch (NoGatewayAvailableException $e) {
                $msg = $e->getException('aliyun')->getMessage();
                abort(500, $msg ?: '短信发送异常');
            }
        }

        $key = Str::random(15);
        $cacheKey = 'verification_code_' . $key;
        $expireAt = now()->addMinutes(5);
        Cache::put($cacheKey, ['phone' => $phone, 'code' => $code], $expireAt);

        return response()->json(['key' => $key, 'expire_at' => $expireAt->toDateTimeString()])->setStatusCode(201);
    }
}
