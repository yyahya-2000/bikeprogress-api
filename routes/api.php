<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('user/add', [AuthController::class, 'register']);
    Route::post('user/edit', [UserController::class, 'edit']);
    Route::post('user/delete', [UserController::class, 'delete']);
    Route::get('users', [UserController::class, 'users']);
    Route::post('user/read', [UserController::class, 'userById']);
    Route::post('user/edit/admin-reset-password', [UserController::class, 'adminresetPassword']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
});
