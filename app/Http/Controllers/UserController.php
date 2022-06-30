<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class UserController extends Controller
{
    public function users(Request $request)
    {
        return User::all();
    }

    public function delete(Request $request)
    {
        try {
            if ($idUser = $request->input('id')) {
                User::query()->where('id', $idUser)->delete();
                return response()->json([
                    'message' => 'success',
                ], Response::HTTP_OK);
            }
        } catch (Exception $e) {
        }
        return response()->json([
            'message' => 'couldn\'t delete the user',
        ], Response::HTTP_BAD_REQUEST);
    }

    public function edit(Request $request)
    {
        $affected = 0;
        try {
            $id = $request->input('id');
            $affected = User::query()->where('id', $id)->update([
                'firstname' => $request->input('firstname'),
                'lastname' => $request->input('lastname'),
                'email' => $request->input('email'),
                'phone_number' => $request->input('phone_number'),
                'position' => $request->input('position'),
            ]);
        } catch (Exception $e) {
        }
        return $affected > 0 ?
            response()->json(['message' => 'success'], Response::HTTP_OK) :
            response()->json(['message' => 'couldn\'t update the user'], Response::HTTP_BAD_REQUEST);
    }
}
