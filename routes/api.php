<?php

use App\Http\Controllers\ConnectedAccountController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Referral\ReferralController;
use App\Http\Controllers\SMS\SmsController;
use App\Http\Controllers\Subscribtions\AuthController;
use App\Http\Controllers\Subscribtions\PlanController;
use App\Http\Controllers\Subscribtions\SubPayment;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// // stripe payment intent
// Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
// Route::post('/success-payment', [PaymentController::class, 'paymentSuccess']);
// Route::post('/payment-link', [PaymentController::class, 'createCheckoutSession']);


// //connected account
// // Route::post('account-create', [ConnectedAccountController::class, 'createAccount'])->name('account-create');
// Route::post('create-connected-account', [ConnectedAccountController::class, 'createStripeConnectedAccount'])->name('account-create');

// Route::get('account-refresh', [ConnectedAccountController::class, 'refreshAccount'])->name('account-refresh');
// Route::get('show-account', [ConnectedAccountController::class, 'showAccount'])->name('show-update');
// Route::post('accounts/{accountId}', [ConnectedAccountController::class, 'updateAccount'])->name('account-update');
// Route::delete('delete-accounts/{accountId}', [ConnectedAccountController::class, 'deleteAccount'])->name('account-delete');


// // Crud customers
// Route::apiResource('customers', CustomerController::class);


// // test.......................
// // Route::get('prams', function (Request $request) {
// //     if ($request->has('ref')) {
// //         return $request->ref;
// //     }
// // });


// // referrals
// Route::post('/register', [ReferralController::class, 'register']);
// Route::get('/parent-with-all-client/{id}', [ReferralController::class, 'parentWithAllClient']);


// // sms sent to phone number twilio sms
// Route::post('/sms', [SmsController::class, 'sendSms']);


// stripe subscription recurring + react js


    Route::get('/plans',[PlanController::class, 'getPlans']);
    Route::post('/login',[AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function(){
        Route::post('/logout',[AuthController::class, 'logout']);
        Route::post('/checkout/{id}',[SubPayment::class, 'checkout']);
        Route::post('/plan',[PlanController::class, 'createPlan']);
    });

    Route::get('/checkout-success',[SubPayment::class,'success'])->name('checkout-success');
    Route::get('/checkout-cancel',[SubPayment::class,'cancel'])->name('checkout-cancel');



    Route::get('/subscription/{id}',[SubPayment::class,'getSubscription']);
    Route::get('/invoice/{id}',[SubPayment::class,'getInvoice']);
    Route::get('/get-subscription-of-customer/{id}',[SubPayment::class,'getSubscriptionOfCustomer']);


    Route::get('/cancel-subscription-now/{id}',[SubPayment::class,'cancelSubscriptionNow']);


// location
Route::get('/map', [MapController::class, 'showMap']);
