<?php

use App\Http\Controllers\API\DataMaster\MasterStockController;
use App\Http\Controllers\API\DataMaster\MasterBrandController;
use App\Http\Controllers\API\DataMaster\DataMasterController;
use App\Http\Controllers\API\DataMaster\MasterBankAccController;
use App\Http\Controllers\API\Apps\PersediaanBarangController;
use App\Http\Controllers\API\Apps\PemakaianBarangController;
use App\Http\Controllers\API\Apps\PatientController;
use App\Http\Controllers\API\Apps\StockOpnameController;
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

            // persediaan barang
            
        });

        // prefix
        Route::group(['prefix' => 'persediaan-barang'], function () {
            // persediaan barang
            Route::get('datatables-detail', [PersediaanBarangController::class, 'datatables_detail']);
            Route::get('datatables_detail_inventory/{fc_stockcode}', [PersediaanBarangController::class, 'datatables_detail_inventory']);
        });

        Route::group(['prefix' => 'pemakaian-barang'], function (){
            Route::post('store-patient', [PatientController::class, 'store']);
            Route::get('status-usage-master', [PemakaianBarangController::class, 'getStatusUsageMaster']);
            Route::delete('delete-usage-master/{fc_patient_id}', [PemakaianBarangController::class, 'deleteUsageMaster']);
            Route::get('detail-barang/{fc_barcode}', [PemakaianBarangController::class, 'getDetailBarang']);
            Route::post('usage-detail', [PemakaianBarangController::class, 'createUsageDetail']);
            Route::get('datatables-usage-detail/{fi_usage_id}', [PemakaianBarangController::class, 'getUsageDetail']);

            // digunakan
            Route::post('scanqr', [PemakaianBarangController::class, 'insert_scanqr']);
            Route::get('new-scanqr', [PemakaianBarangController::class, 'getNewScanqr']);
        });

        Route::group(['prefix' => 'stock-opname'], function(){
            Route::get('master', [StockOpnameController::class, 'index']);
            Route::get('datatable-persediaan/{fc_warehousecode}', [StockOpnameController::class, 'get_datatables_persediaan']);
            Route::post('master', [StockOpnameController::class, 'createStockopnameMaster']);
            Route::get('exist-stockopname-master', [StockOpnameController::class, 'getStatusStockopnameMaster']);
            Route::delete('delete-temp-stockopname', [StockOpnameController::class, 'deleteTempStockOpname']);
            Route::post('detail/select-stock', [StockOpnameController::class, 'select_stock']);
            Route::get('stockopname-detail', [StockOpnameController::class, 'getStockopnameDetail']);
            Route::delete('stockopname-detail/{rownum}', [StockOpnameController::class, 'deleteStockOpnameDetail']);
            Route::post('submit-stockopname', [StockOpnameController::class, 'submit_stockopname']);
        });

        //prefix
        Route::group(['prefix' => 'penerimaan-barang'], function () {
            // persediaan barang
            Route::get('', [PersediaanBarangController::class, 'datatables_detail']);
            Route::get('datatables_detail_inventory/{fc_stockcode}', [PersediaanBarangController::class, 'datatables_detail_inventory']);
        });
    // });
});
