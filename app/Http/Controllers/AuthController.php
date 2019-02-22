<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Http\Resources\User as UserResource;
use App\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            throw new BadRequestHttpException('Invalid email or password');
        }

        if (!app('hash')->check($request->input('password'), $user->password)) {
            throw new BadRequestHttpException('Invalid email or password');
        }

        $user->generateToken();
        $user->save();

        return UserResource::make($user);
    }
}
