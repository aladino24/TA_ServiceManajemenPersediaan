<?php

use App\Http\Controllers\API\DataMaster\MasterStockController;
use App\Http\Controllers\API\DataMaster\MasterBrandController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'check-authentication'], function () {
    Route::group(['prefix' => 'master'], function () {

        // master stock
        Route::get('stock', [MasterStockController::class, 'getStock']);
        Route::post('stock', [MasterStockController::class, 'createStock']);
        Route::put('stock', [MasterStockController::class, 'updateStock']);
        Route::delete('stock/{fc_stockcode}/{fc_barcode}', [MasterStockController::class, 'deleteStock']);
        Route::get('brand', [MasterBrandController::class, 'getBrand']);
       
        // Route::get('stock/{id}', 'API\DataMaster\MasterStockController@getStockById');
        // Route::post('stock', 'API\DataMaster\MasterStockController@createStock');
        // Route::put('stock/{id}', 'API\DataMaster\MasterStockController@updateStock');
        // Route::delete('stock/{id}', 'API\DataMaster\MasterStockController@deleteStock');
    });
});
