<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Api\AnnoucementsController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post("register", [ApiController::class, "register"]);
Route::post("login", [ApiController::class, "login"]);

Route::group([
    "middleware" => ["auth:api"]
], function(){
    Route::get("profile", [ApiController::class, "profile"]);
    Route::get("refresh", [ApiController::class, "refreshToken"]);
    Route::get("logout", [ApiController::class, "logout"]);
});

Route::get('annoucements',[AnnoucementsController::class,'index']);
Route::post('annoucement',[AnnoucementsController::class,'store']);
Route::get('annoucements/{id}',[AnnoucementsController::class,'show']);
Route::get('annoucements/{id}/edit',[AnnoucementsController::class,'edit']);
Route::put('updateAnnoucements/{id}/edit',[AnnoucementsController::class,'update']);
Route::delete('annoucements/{id}/delete',[AnnoucementsController::class,'destroy']);

// Route::get('reservations',[ReservationController::class,'apply']);
// Route::put('updatereservations',[ReservationController::class,'apply']);









// Route::controller(ReservationController::class)->group(function () {
//     Route::get('dataReservations', 'index');
//     Route::put('updateStatut/{id}', 'updateStatut');
// });
// Route::controller(ReservationController::class)->group(function () {
//     Route::post('storeReservation/{id}', 'store');
// });
Route::post('storeReservation/{id}', [ReservationController::class, 'store']);
Route::get('dataReservations', [ReservationController::class, 'index']);
Route::put('updateStatut/{id}',[ReservationController::class,'updateStatut']);
