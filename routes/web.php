<?php

use App\Http\Controllers\StripePaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::controller(StripePaymentController::class)->group(function () {
    Route::get('stripe', 'stripe');
    Route::post('stripe', 'stripePost')->name('stripe.post');
});
