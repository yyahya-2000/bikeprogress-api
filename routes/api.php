<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PurchaseController;
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

    Route::get('contacts', [ContactController::class, 'index']);
    Route::get('contact/read/{id}', [ContactController::class, 'show']);
    Route::post('contact/edit', [ContactController::class, 'update']);
    Route::post('contact/delete', [ContactController::class, 'destroy']);
    Route::post('contact/add', [ContactController::class, 'create']);

    Route::get('certificates', [CertificateController::class, 'index']);
    Route::get('certificate/read/{id}', [CertificateController::class, 'show']);
    Route::post('certificate/edit', [CertificateController::class, 'update']);
    Route::post('certificate/delete', [CertificateController::class, 'destroy']);
    Route::post('certificate/add', [CertificateController::class, 'add']);

    Route::get('purchases', [PurchaseController::class, 'index']);
    Route::get('purchase/read/{id}', [PurchaseController::class, 'show']);
    Route::post('purchase/edit', [PurchaseController::class, 'update']);
    Route::post('purchase/delete', [PurchaseController::class, 'destroy']);
    Route::post('purchase/add', [PurchaseController::class, 'add']);


});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
});
