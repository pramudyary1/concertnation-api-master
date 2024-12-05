<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
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

Route::group(["middleware"=>'auth:sanctum'], function() {
    Route::apiResource('user',UserController::class);
    Route::apiResource('event',EventController::class);
    Route::apiResource('order',OrderController::class);
    Route::post('event/publish/{id}', [EventController::class, 'publish']);
    Route::get('dashboard/admin',[DashboardController::class, 'admin']);
    Route::get('dashboard/promotor/{id}',[DashboardController::class, 'promotor']);
});
Route::get('promotor/order/{id}', [OrderController::class, 'getOrderByPromotor']);

Route::get('latest',[EventController::class, 'latest']);
Route::get('popular',[EventController::class, 'popular']);
Route::get('featured',[EventController::class, 'featured']);
Route::get('detail/{id}',[EventController::class, 'show']);

Route::get('/unauthorized', function (Request $request) {
    return response(['message'=>'unauthorized'],400);
})->name('noauth');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

