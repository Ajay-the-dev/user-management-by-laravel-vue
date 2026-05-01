<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\NoticeController;



//users

Route::post('/users/authenticate', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('users')->group(function () {
                Route::post('/create', [UserController::class, 'store']);
                Route::get('/', [UserController::class, 'index']);
                Route::put('/{id}', [UserController::class, 'update']);
                Route::delete('/{id}', [UserController::class, 'remove']);
                Route::get('/{id}', [UserController::class, 'resetPassword']);
                Route::post('byParams', [UserController::class, 'filterByParams']);
                Route::post('/find', [UserController::class, 'findByName']);
                Route::post('/checking',[UserController::class, 'isChecking']);
                Route::post('/getById',[UserController::class, 'getUserById']);
                Route::get('/bybatchId/{id}',[UserController::class, 'getStudentsByBatch']);
        });
});

//batches
Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('batches')->group(function () {
                Route::post('/create', [BatchController::class, 'store']);
                Route::get('/', [BatchController::class, 'index']);
                Route::get('/all', [BatchController::class, 'getAll']);
                Route::put('/{id}', [BatchController::class, 'update']);
                Route::delete('/{id}', [BatchController::class, 'remove']);
                Route::post('/find', [BatchController::class, 'findByName']);
        });
});

        
//images
Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('images')->group(function () {
                Route::post('/upload-image', [ImageController::class, 'upload']);
                Route::post('/remove', [ImageController::class, 'deletePicture']);
        });

});


//fee
Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('fees')->group(function () {
                Route::get('/', [FeeController::class, 'index']);
                Route::get('/bybatch/{id}',[FeeController::class, 'getFeesByBatch']);
                Route::post('/feePayment',[FeeController::class, 'insertPayment']);
                Route::get('/paymentSummary/{id}',[FeeController::class, 'getStudentFeeSummary']);
                Route::post('/paidFee',[FeeController::class, 'getFeeSummaryByFeeId']);
                Route::get('/yearly',[FeeController::class, 'getYearlyReportWithRecents']);
        });
});


//document
Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('documents')->group(function (){
                Route::post('/upload',[DocumentController::class,'upload']);
                Route::post('/userdocs',[DocumentController::class,'documentEntryForUser']);
                Route::get('/{id}',[DocumentController::class,'getDocumentsByUser']);
                Route::delete('/{id}',[DocumentController::class,'remove']);
        });
});


//notice
Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('notice')->group(function (){
                Route::post('/create',[NoticeController::class,'createNotice']);
                Route::get('all',[NoticeController::class,'index']);
                Route::get('getDetails/{id}',[NoticeController::class,'getDetailsById']);
        });
});


