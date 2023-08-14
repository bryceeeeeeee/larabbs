<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\CaptchaRequest;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class CaptchasController extends Controller
{
    //
    public function store(CaptchaRequest $request, CaptchaBuilder $builder)
    {
        $key = Str::random(15);
        $cacheKey = 'captcha_' . $key;
        $phone = $request->phone;

        $captcha = $builder->build();
        $exporedAt = now()->addMinutes(2);

        Cache::put($cacheKey, ['phone' => $phone, 'code' => $captcha->getPhrase()], $exporedAt);

        $res = [
            'captcha_key' => $key,
            'expired_at' => $exporedAt->toDateTimeString(),
            'captcha_image_content' => $captcha->inline()
        ];

        return response()->json($res)->setStatusCode(201);
    }
}
