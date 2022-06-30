<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if (Auth::check() && mb_strtolower(Auth::user()->position) === 'администратор') {
                return $next($request);
            }
        } catch (Exception $e) {
        }

        return response([
            'message' => 'you are not allowed to access this'
        ], Response::HTTP_LOCKED);
    }
}
