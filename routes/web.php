<?php

use App\Http\Controllers\MapController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\StripePaymentController;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
});


// // stripe payment............................................................
// Route::controller(StripePaymentController::class)->group(function () {
//     Route::get('stripe', 'stripe');
//     Route::post('stripe', 'stripePost')->name('stripe.post');
// });


// // session.........................................
// Route::get('/set', function () {
//     Session::put('user', 'Sohan');
//     return 'Session set!';
// });

// Route::get('/get', function () {
//     return Session::get('user');
// });


// // cookies..........................................
// Route::get('/set-cookie', function () {
//     Cookie::queue('language', 'bangla', 30);
//     return 'Cookie set!';
// });

// Route::get('/get-cookie', function () {
//     $lang = Cookie::get('language');
//     return 'Language is: ' . $lang;
// });

// Route::get('/delete-cookie', function () {
//     Cookie::queue(Cookie::forget('language'));
//     return 'Cookie deleted!';
// });


// // pdf

// Route::get('/pdf/show', [PdfController::class, 'show']);
// Route::get('/pdf/download', [PdfController::class, 'download']);
// Route::get('/pdf/store', [PdfController::class, 'store']);
// Route::get('/pdf/show/{id}', [PdfController::class, 'viewStored']);
