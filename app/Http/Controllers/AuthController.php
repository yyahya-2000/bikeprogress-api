<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        return User::create([
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'password' => Hash::make($request->input('password')),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'position' => $request->input('position'),
        ]);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only(['password', 'email']))) {
            return response([
                'message' => 'Invalid login or password',
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        $token = $user->createToken('token')->plainTextToken;
        $cookie = cookie('jwt', $token, 60 * 24);

        return response([
            'message' => 'success',
        ])->withCookie($cookie);
    }

    public function user()
    {
        return Auth::user();
    }

    public function logout(Request $request)
    {
        $cookie = cookie::forget('jwt');

        $request->user()->tokens()->delete();

        return response([
            'message' => 'success',
        ])->withCookie($cookie);
    }
}
