<?php

use App\Http\Controllers\Api\{
  AuthController,
  ProductController,
  HoldController,
  OrderController,
};
use App\Utils\Response;
use Illuminate\Support\Facades\Route;


Route::name("api.")->group(function () {
  Route::prefix("auth")->name("auth.")->group(function () {
    Route::post("register", [AuthController::class, "register"])->name("register");
    Route::post("login", [AuthController::class, "login"])->name("login");
  });

  Route::prefix("products")->name("product.")->group(function () {
    Route::get("/", [ProductController::class, "index"])->name("index");
    Route::get("/{id}", [ProductController::class, "show"])->name("show");
  });

  Route::middleware("auth:sanctum")->group(function () {
    Route::prefix("holds")->name("hold.")->group(function () {
      Route::post("/", [HoldController::class, "store"])->name("store");
    });
    Route::prefix("orders")->name("order.")->group(function () {});
  });

  Route::prefix("payment")->name("payment.")->group(function () {});



  Route::prefix("information")->name("version-data.")->group(function () {
    Route::get("/health", function () {
      return Response::success([
        'version' => "v1",
        'status' => "OK",
        'database-status' => "OK",
        'redis-status' => "OK"
      ]);
    });
  });
});
