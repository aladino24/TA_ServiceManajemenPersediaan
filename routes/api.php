<?php

use App\Http\Controllers\API\DataMaster\MasterStockController;
use App\Http\Controllers\API\DataMaster\MasterBrandController;
use App\Http\Controllers\API\DataMaster\DataMasterController;
use App\Http\Controllers\API\DataMaster\MasterBankAccController;
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

// Template Route
Route::get('/get-data-where-field-id-get/{model}/{where_field}/{id}', [DataMasterController::class, 'get_data_where_field_id_get']);
// Data Master Stock
Route::get('/get-brand', [DataMasterController::class, 'getBrand']);
Route::get('/get-unity', [DataMasterController::class, 'getUnity']);
Route::get('/stock-group-by-unity', [DataMasterController::class, 'getGroupByBrand']);
Route::get('/stock-subgroup-by-group', [DataMasterController::class, 'getSubgroupByGroup']);

Route::group(['middleware' => 'check-authentication'], function () {
    // Route::group(['middleware' => 'cors'], function(){
        Route::group(['prefix' => 'master'], function () {

            // master stock
            Route::get('stock', [MasterStockController::class, 'getStock']);
            Route::get('detail-stock/{fc_stockcode}/{fc_barcode}', [MasterStockController::class, 'detailStock']);
            Route::post('stock', [MasterStockController::class, 'createStock']);
            Route::put('stock', [MasterStockController::class, 'updateStock']);
            Route::delete('stock/{fc_stockcode}/{fc_barcode}', [MasterStockController::class, 'deleteStock']);
            Route::put('stock/hold/{fc_barcode}', [MasterStockController::class, 'holdStock']);
            Route::put('stock/unhold/{fc_barcode}', [MasterStockController::class, 'unholdStock']);
    
            // master brand
            Route::get('brand/datatables', [MasterBrandController::class, 'datatables']);
            Route::get('brand', [MasterBrandController::class, 'getBrand']);
            Route::post('brand', [MasterBrandController::class, 'createBrand']);
            Route::put('brand', [MasterBrandController::class, 'updateBrand']);
            Route::delete('brand/{id}', [MasterBrandController::class, 'deleteBrand']);


            // Master Bank Acc
            Route::get('bank-acc', [MasterBankAccController::class, 'getBankAcc']);
            Route::post('bank-acc', [MasterBankAccController::class, 'createBankAcc']);
            Route::put('bank-acc', [MasterBankAccController::class, 'updateBankAcc']);
            Route::delete('bank-acc/{id}', [MasterBankAccController::class, 'deleteBankAcc']);

            // Route::get('stock/{id}', 'API\DataMaster\MasterStockController@getStockById');
            // Route::post('stock', 'API\DataMaster\MasterStockController@createStock');
            // Route::put('stock/{id}', 'API\DataMaster\MasterStockController@updateStock');
            // Route::delete('stock/{id}', 'API\DataMaster\MasterStockController@deleteStock');
        });
    // });
});
