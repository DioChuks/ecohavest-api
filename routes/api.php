<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// verify otp from user
Route::post('email/otp/verify', [EmailVerificationController::class, 'verifyOtpEmail'])->middleware(['auth:api', 'throttle:6,1']);

// resend otp to user email
Route::post('email/otp/resend', [EmailVerificationController::class, 'resendOtpVerification'])->middleware(['auth:api', 'throttle:6,1']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// for swagger implementation
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/newsletter-subscribe', [NewsletterController::class, 'subscribe']);


// Protecting our routes from unauthorized requests
// Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function(){

    Route::post('/check-auth', [AuthController::class, 'checkAuth']);
    
    // reset user password
    Route::post('/user/password-reset', [AuthController::class, 'changeGeneratedForgotPassword']);

    Route::put('/user/{id}', [UserController::class, 'update']);

    // to log the user out
    Route::post('/logout', [AuthController::class, 'logout']);
});
