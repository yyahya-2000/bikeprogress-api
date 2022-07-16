<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $rules = array(
            'firstname' => 'required',
            'lastname' => 'required',
            'password' => ['required', 'min:6', 'max:40', 'regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/'],
            'email' => 'required|unique:users',
            'phone_number' => [
                'required',
                'unique:users',
                'regex:/^(\+7|7|8)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/'
            ],
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

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
            return response()->json([
                'message' => 'Invalid login or password',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        $token = $user->createToken('token')->plainTextToken;
        $cookie = cookie('jwt', $token, 60 * 24);

        return response()->json([
            'message' => 'success',
        ])->withCookie($cookie);
    }

    public function user()
    {
        return Auth::user();
    }

    public function logout(Request $request)
    {
        //Auth::logout();
        $jwtCookie = cookie::forget('jwt');
        $laravelSessionCookie = cookie::forget('laravel_session');
//
       Auth::user()->tokens()->delete();
//
        return response()->json([
            'message' => 'success',
       ])->withCookie($jwtCookie)->withCookie($laravelSessionCookie);
    }
}
