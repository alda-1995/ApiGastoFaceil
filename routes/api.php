<?php

use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\Costos\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix("auth")->group(function () {
    Route::post("create-user", [AuthController::class, "createUser"]);
    Route::post("login", [AuthController::class, "authenticate"]);
    Route::post("logout", [AuthController::class, "logout"]);
});

Route::prefix("products")->group(function () {
    Route::get("all-products/{idUser}", [ProductController::class, "allProducts"])->middleware('auth:sanctum');
    Route::get("detail-product/{idProduct}", [ProductController::class, "detailProduct"])->middleware('auth:sanctum');
    Route::post("create-product", [ProductController::class, "createProduct"])->middleware('auth:sanctum');
    Route::put("update-product/{idProduct}", [ProductController::class, "updateProduct"])->middleware('auth:sanctum');
});