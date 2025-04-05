<?php

use App\Http\Controllers\ConnectedAccountController;
use App\Http\Controllers\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
Route::post('/success-payment', [PaymentController::class, 'paymentSuccess']);



//connected account
Route::post('account-create', [ConnectedAccountController::class, 'createAccount'])->name('account-create');


Route::get('account-refresh', [ConnectedAccountController::class, 'refreshAccount'])->name('account-refresh');
Route::get('show-account', [ConnectedAccountController::class, 'showAccount'])->name('show-update');
Route::post('accounts/{accountId}', [ConnectedAccountController::class, 'updateAccount'])->name('account-update');
Route::delete('delete-accounts/{accountId}', [ConnectedAccountController::class, 'deleteAccount'])->name('account-delete');


Route::apiResource('customers',CustomerController::class);
