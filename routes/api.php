<?php

use App\Http\Controllers\Api\{
  AuthController,
  ProductController,
  HoldController,
  OrderController,
};

use Illuminate\Support\Facades\Route;


Route::name("api.")->group(function () {
  Route::prefix("auth")->name("auth.")->group(function () {
    Route::post("register", [AuthController::class, "register"])->name("register");
    Route::post("login", [AuthController::class, "login"])->name("login");
  });
});
