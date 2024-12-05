<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/email/verify/{id}/{hash}', function ( $request) {
    // $request->fulfill();
    $user = User::find($request);
    $user->email_verified_at = now();
    $user->save();

    return view('verified');
})->name('verification.verify');
Route::get('/', function () {
    return view('welcome');
});
