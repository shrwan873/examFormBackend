<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Controllers\Api\PaymentController;

Route::post('/auth/register', [AuthController::class,'register']);
Route::post('/auth/login', [AuthController::class,'login']);

Route::post('/webhook/payment', [PaymentController::class,'webhook']);

Route::middleware('jwt.auth')->group(function () {
    Route::post('/auth/logout',[AuthController::class,'logout']);
    Route::get('/auth/me',[AuthController::class,'me']);

    Route::get('/forms',[FormController::class,'index']);
    Route::get('/forms/{id}',[FormController::class,'show']);

    Route::post('/submissions',[SubmissionController::class,'store']);
    Route::get('/submissions/my',[SubmissionController::class,'mySubmissions']);

    Route::post('/payments/initiate',[PaymentController::class,'initiate']);
    Route::post('/payments/confirm',[PaymentController::class,'confirmPayment']);
    Route::get('/payments/{id}/receipt',[PaymentController::class,'downloadReceipt']);

    Route::group(['middleware' => 'role:admin'], function(){
        Route::post('/forms',[FormController::class,'store']);
        Route::put('/forms/{id}',[FormController::class,'update']);
        Route::delete('/forms/{id}',[FormController::class,'destroy']);

        Route::get('/admin/submissions', [SubmissionController::class,'index']);
        Route::get('/admin/submissions/{id}', [SubmissionController::class,'show']);
    });
});
