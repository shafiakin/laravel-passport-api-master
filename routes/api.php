<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OrderController;


// Open Routes
Route::post("register", [ApiController::class, "register"]);
Route::post("login", [ApiController::class, "login"]);
Route::post('/customers', [CustomerController::class, 'store']);
//Protected Routes
Route::group([
    "middleware" => ["auth:api"]
], function(){
    Route::get("/profile", [ApiController::class, "profile"]);
    Route::get("/logout", [ApiController::class, "logout"]);

    
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::put('/customers/{id}', [CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
    Route::get('/customers/{id}/orders', [CustomerController::class, 'getCustomerWithOrders']);


    Route::apiResource('orders', OrderController::class);
});





