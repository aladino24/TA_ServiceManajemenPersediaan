<?php

namespace App\Http\Controllers\API\DataMaster;

use App\Helpers\Convert;
use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MasterStockController extends Controller
{
    public function getStock(){
        $stock = Stock::with(['branch', 'namepack', 'type_stock1', 'type_stock2'])->get();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil menampilkan data stock',
            'data' => $stock
        ], 200);
    }

    public function createStock(Request $request){
        // validator
        $validator = Validator::make($request->all(), [
            'fc_stockcode' => 'required',
            'fc_barcode' => 'required',
        ]);

        $user = Session::get('user');
        $branch = $user['user']['branch'];
        // response error validator
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $request->request->add(['fc_branch' => $branch]);
        if(empty($request->type)){
            $cek_data = Stock::where([
                'fc_stockcode' => $request->fc_stockcode,
                'deleted_at' => null,
            ])->withTrashed()->count();

            if($cek_data > 0){
                return [
                    'status' => 300,
                    'message' => 'Oops! Insert gagal karena data sudah ditemukan didalam sistem kami'
                ];
            }
        }

        $request->merge(['fn_reorderlevel' => doubleval($request->fn_reorderlevel) ]);
        $request->merge(['fn_maxonhand' => doubleval($request->fn_maxonhand) ]);
        $request->merge(['fm_cogs' => doubleval($request->fm_cogs) ]);
        $request->merge(['fm_purchase' => doubleval($request->fm_purchase) ]);
        $request->merge(['fm_salesprice' => doubleval($request->fm_salesprice) ]);

        $request->merge(['fm_purchase' => doubleval($request->fm_purchase) ]);
        $request->merge(['fm_salesprice' => doubleval($request->fm_salesprice) ]);
        $request->merge(['fm_disc_pr' => doubleval($request->fm_disc_pr) ]);
        $request->merge(['fm_disc_rp' => doubleval($request->fm_disc_rp) ]);
        $request->merge(['fm_time_disc_rp' => doubleval($request->fm_time_disc_rp) ]);
        $request->merge(['fm_time_disc_pr' => doubleval($request->fm_time_disc_pr) ]);
        $request->merge(['fm_price_default' => doubleval($request->fm_price_default) ]);
        $request->merge(['fm_price_distributor' => doubleval($request->fm_price_distributor) ]);
        $request->merge(['fm_price_project' => doubleval($request->fm_price_project) ]);
        $request->merge(['fm_price_dealer' => doubleval($request->fm_price_dealer) ]);
        $request->merge(['fm_price_enduser' => doubleval($request->fm_price_enduser) ]);


        // insert
        try {
            $stock = Stock::create($request->all());
            return response()->json([
                'status' => 200,
                'message' => 'Berhasil menambahkan data stock',
                'data' => $stock
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Oops! Terjadi kesalahan saat menambahkan data stock',
                'data' => $th->getMessage()
            ], 500);
        }
    }

    public function detailStock($fc_stockcode, $fc_barcode){
        $stockcodeDecode = base64_decode($fc_stockcode);
        $barcodeDecode = base64_decode($fc_barcode);
        $user = Session::get('user');
        $branch = $user['user']['branch'];

        $stock = Stock::where('fc_stockcode', $stockcodeDecode)
        ->where('fc_barcode', $barcodeDecode)
        ->where('fc_branch', $branch)
        ->first();

        if($stock){
            return response()->json([
                'status' => 200,
                'message' => 'Berhasil menampilkan detail stock',
                'data' => $stock
            ], 200);
        }

        return response()->json([
            'status' => 404,
            'message' => 'Oops! Data stock tidak ditemukan',
            'data' => null
        ], 404);

    }
    
    public function updateStock(Request $request){
        // validator
        $validator = Validator::make($request->all(), [
            'fc_stockcode' => 'required',
            'fc_barcode' => 'required',
        ]);

        $user = Session::get('user');
        $branch = $user['user']['branch'];
        // response error validator
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $request->request->add(['fc_branch' => $branch]);

        $request->merge(['fn_reorderlevel' => doubleval($request->fn_reorderlevel) ]);
        $request->merge(['fn_maxonhand' => doubleval($request->fn_maxonhand) ]);
        $request->merge(['fm_cogs' => doubleval($request->fm_cogs) ]);
        $request->merge(['fm_purchase' => doubleval($request->fm_purchase) ]);
        $request->merge(['fm_salesprice' => doubleval($request->fm_salesprice) ]);

        $request->merge(['fm_purchase' => doubleval($request->fm_purchase) ]);
        $request->merge(['fm_salesprice' => doubleval($request->fm_salesprice) ]);
        $request->merge(['fm_disc_pr' => doubleval($request->fm_disc_pr) ]);
        $request->merge(['fm_disc_rp' => doubleval($request->fm_disc_rp) ]);
        $request->merge(['fm_time_disc_rp' => doubleval($request->fm_time_disc_rp) ]);
        $request->merge(['fm_time_disc_pr' => doubleval($request->fm_time_disc_pr) ]);
        $request->merge(['fm_price_default' => doubleval($request->fm_price_default) ]);
        $request->merge(['fm_price_distributor' => doubleval($request->fm_price_distributor) ]);
        $request->merge(['fm_price_project' => doubleval($request->fm_price_project) ]);

        $request->merge(['fm_price_dealer' => doubleval($request->fm_price_dealer) ]);
        $request->merge(['fm_price_enduser' => doubleval($request->fm_price_enduser) ]);

        // update
        try {
            $stock = Stock::where('fc_stockcode', $request->fc_stockcode)
            ->where('fc_barcode', $request->fc_barcode)
            ->update($request->all());
            return response()->json([
                'status' => 200,
                'message' => 'Berhasil mengubah data stock',
                'data' => $stock
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Oops! Terjadi kesalahan saat mengubah data stock',
                'data' => $th->getMessage()
            ], 500);
        }
    }

    public function deleteStock($fc_stockcode, $fc_barcode){
        $user = Session::get('user');
        $branch = $user['user']['branch'];

        // Decode fc_stockcode and fc_barcode
        $decode_fc_stockcode = base64_decode($fc_stockcode);
        $decode_fc_barcode = base64_decode($fc_barcode);

        try {
            $delete = Stock::where([
                'fc_stockcode' => $decode_fc_stockcode,
                'fc_barcode' => $decode_fc_barcode,
                'fc_branch' => $branch
            ])->delete();

            if ($delete) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Berhasil menghapus data stock',
                    'data' => $delete
                ], 200);
            } else {
                // Handle the case where the delete operation did not delete any records
                return response()->json([
                    'status' => 404,
                    'message' => 'Data stock tidak ditemukan atau tidak dapat dihapus',
                    'data' => null
                ], 404);
            }
        } catch (\Throwable $th) {
            // Handle other exceptions
            return response()->json([
                'status' => 500,
                'message' => 'Oops! Terjadi kesalahan saat menghapus data stock',
                'data' => $th->getMessage()
            ], 500);
        }
    }


    public function holdStock(Request $request, $fc_barcode){
        $decode_fc_barcode = base64_decode($fc_barcode);
        $user = Session::get('user');
        $branch = $user['user']['branch'];

        $fc_hold = $request->fc_hold;

        $stock = Stock::where('fc_barcode', $decode_fc_barcode)
        ->where('fc_branch', $branch)
        ->first();

        $update_status = $stock->update([
            'fc_hold' => $fc_hold,
        ]);

        if($update_status){
            return response()->json([
                'status' => 200,
                'message' => 'Data Stock berhasil di hold',
                'data' => $update_status
            ], 200);
        }

        return [
            'status' => 300,
            'message' => 'Data gagal di hold'
        ];
    }


    public function unholdStock(Request $request, $fc_barcode){
        $decode_fc_barcode = base64_decode($fc_barcode);
        $user = Session::get('user');
        $branch = $user['user']['branch'];

        $fc_hold = $request->fc_hold;

        $stock = Stock::where('fc_barcode', $decode_fc_barcode)
        ->where('fc_branch', $branch)
        ->first();

        $update_status = $stock->update([
            'fc_hold' => $fc_hold,
        ]);

        if($update_status){
            return response()->json([
                'status' => 200,
                'message' => 'Data Stock berhasil di unhold',
                'data' => $update_status
            ], 200);
        }

        return [
            'status' => 300,
            'message' => 'Data gagal di unhold'
        ];
    }

}
