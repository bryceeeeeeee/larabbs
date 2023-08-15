<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\AuthenticationException;
use App\Models\User;
use App\Models\Image;

class UsersController extends Controller
{
    //
    public function store(UserRequest $request)
    {
        $cacheKey = 'verification_code_' . $request->verification_key;
        $verifyData = Cache::get($cacheKey);

        if (! $verifyData) {
            abort(403, '验证码已失效');
        }

        if (! hash_equals($verifyData['code'], $request->verification_code)) {
            throw new AuthenticationException('验证码错误');
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => $request->password
        ]);

        Cache::forget($cacheKey);

        return (new UserResource($user))->showSensitiveFields();
    }

    public function show(User $user, Request $request)
    {
        return new UserResource($user);
    }

    public function me(Request $request)
    {
        return (new UserResource($request->user()))->showSensitiveFields();
    }

    public function update(UserRequest $request)
    {
        $user = $request->user();

        $attributes = $request->only(['name', 'email', 'introduction', 'registration_id']);

        if ($request->avatar_image_id) {
            $image = Image::find($request->avatar_image_id);

            $attributes['avatar'] = $image->path;
        }

        $user->update($attributes);

        return (new UserResource($user))->showSensitiveFields();
    }

    public function activeIndex(User $user)
    {
        UserResource::wrap('data');

        return UserResource::collection($user->getActiveUsers());
    }
}
