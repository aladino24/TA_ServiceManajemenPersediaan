<?php

namespace App\Http\Controllers\API\Apps;

use App\Http\Controllers\Controller;
use App\Models\Invstore;
use App\Models\StockOpnameMaster;
use App\Models\StockOpnameDetail;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Helpers\DocNumber;
use Validator;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{

    public function index(){
        // session
        $user = Session::get('user');
        $userid = $user['user']['userid'];
        $branch = $user['user']['branch'];
        $divisioncode = $user['user']['divisioncode'];
        $membercode = $user['user']['fc_membercode'];
        $membername = $user['user']['ascustomer']['fv_membername'];
        $member_address = $user['user']['ascustomer']['fv_memberaddress'];

        $stockopname_master = StockOpnameMaster::where('fc_stockopname_no', $userid)
                                ->where('fc_membercode', $membercode)
                                 ->first();
        
        $stockopname_detail = StockOpnameDetail::where('fc_stockopname_no', $userid)
                                ->where('fc_membercode', $membercode)
                                ->get();

        $total = count($stockopname_detail);

        $jumlah_stock = Invstore::where('fc_branch', $branch)
                                    ->where('fc_membercode', $membercode)
                                    ->count();

        
        $data_user['branch'] = $branch;
        $data_user['userid'] = $userid;
        $data_user['membercode'] = $membercode;
        $data_user['divisioncode'] = $divisioncode;
        $data_user['jumlah_stock'] = $jumlah_stock;
        $data_user['total_opname'] = $total;
        $data_user['membername'] = $membername;
        $data_user['member_address'] = $member_address;

        if(!empty($stockopname_master)){
            $stock_teropname = StockOpnameDetail::where('fc_stockopname_no', $userid)
            ->where('fc_status', 'L')
            ->where('fc_branch', $branch)
            ->where('fc_membercode', $membercode)
            ->count();

            $data_user['stock_teropname'] = $stock_teropname;
        }

        return response()->json([
            'success' => true,
            'message' => 'Data ditemukan',
            'data' => $data_user,
        ], 200);

    }

    public function get_datatables_persediaan($fc_warehousecode){

        // decode warehousecode
        $warehousecode = base64_decode($fc_warehousecode);

        $user = Session::get('user');
        $branch = $user['user']['branch'];
        $membercode = $user['user']['fc_membercode'];
        
        $data = Invstore::with('stock')
            ->where('fc_branch', $branch)
            ->where('fc_membercode', $membercode)
            ->where('fc_warehousecode', $warehousecode)
            ->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }


    public function createStockopnameMaster(Request $request){
        // validator
        $validator = Validator::make($request->all(), [
            'fd_stockopname_start' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->all()
            ], 400);
        }

        $user = Session::get('user');
        $branch = $user['user']['branch'];
        $divisioncode = $user['user']['divisioncode'];
        $userid = $user['user']['userid'];
        $membercode = $user['user']['fc_membercode'];
        

        $stockopname_master = StockOpnameMaster::where('fc_stockopname_no', $userid)
                                ->where('fc_membercode', $membercode)
                                ->where('fc_branch', $branch)
                                ->first();

        if(empty($stockopname_master)){
            $startDate = Carbon::parse($request->fd_stockopname_start);
            $formattedStartDate = $startDate->format('Y-m-d H:i:s');
            $insert = StockOpnameMaster::create([
                'fc_branch' => $branch,
                'fc_divisioncode' => $divisioncode,
                'fc_stockopname_no' => $userid,
                'fc_membercode' => $membercode,
                'fc_warehousecode' => 'WRHS0000001',
                'fd_stockopname_start' => $formattedStartDate,
                'fc_stockopname_status' => 'I',
                'fc_userid' => $userid,
            ]);

            if($insert){
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil disimpan',
                ], 200);    
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Data gagal disimpan',
                ], 400);
            }
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Data sudah ada',
            ], 400);
        }
        
    }

    public function getStatusStockopnameMaster(){
        $user = Session::get('user');
        $branch = $user['user']['branch'];
        $membercode = $user['user']['fc_membercode'];
        $userid = $user['user']['userid'];

        $stockopname_master = StockOpnameMaster::with('ascustomer')->where('fc_stockopname_no', $userid)
                                ->where('fc_membercode', $membercode)
                                ->where('fc_branch', $branch)
                                ->first();

        if(!empty($stockopname_master)){
            return response()->json([
                'success' => true,
                'message' => 'Data ditemukan',
                'data' => $stockopname_master,
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 201);
        }
    }


    public function deleteTempStockOpname(){
            $user = Session::get('user');
            $branch = $user['user']['branch'];
            $membercode = $user['user']['fc_membercode'];
            $userid = $user['user']['userid'];

            DB::beginTransaction();

            try {
                // Cari master data yang akan dihapus
                
                StockOpnameDetail::where('fc_stockopname_no', $userid)
                        ->where('fc_membercode', $membercode)
                        ->delete();
                StockOpnameMaster::where('fc_stockopname_no', $userid)
                ->where('fc_membercode', $membercode)
                ->delete();
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil dihapus',
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ], 500);
            }
        }

    public function select_stock(Request $request){
        $validator = Validator::make($request->all(), [
            'fc_barcode' => 'required',
            'fn_quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 300,
                'message' => $validator->errors()->first()
            ];
        }

        $user = Session::get('user');
        $branch = $user['user']['branch'];
        $divisioncode = $user['user']['divisioncode'];
        $membercode = $user['user']['fc_membercode'];
        $userid = $user['user']['userid'];

        $stockopname_detail = StockOpnameDetail::where('fc_stockopname_no', $userid)
                                        ->where('fc_membercode', $membercode)
                                        ->orderBy('fn_rownum', 'DESC')
                                        ->first();
        
        $count_barcode = StockOpnameDetail::where('fc_stockopname_no', $userid)
                                            ->where('fc_membercode', $membercode)
                                            ->where('fc_barcode', $request->fc_barcode)
                                            ->get();

        if(!empty($stockopname_detail)){
            if (count($count_barcode) > 0) {
                return [
                    'success' => false,
                    'status' => 300,
                    'message' => 'Produk yang sama telah diinputkan'
                ];
            }
        }

        $create_stockopname_detail = StockOpnameDetail::create([
            'fc_divisioncode' => $divisioncode,
            'fc_branch' => $branch,
            'fc_userid' => $userid,
            'fc_stockopname_no' => $userid,
            'fc_barcode' => $request->fc_barcode,
            'fc_membercode' => $membercode,
            'fc_warehousecode' => 'WRHS0000001',
            'fn_quantity' => $request->fn_quantity,
            'fc_status' => 'L',
        ]);

        if($create_stockopname_detail){
            return [
                'success' => true,
                'status' => 200,
                'message' => 'Data berhasil disimpan'
            ];
        }else{
            return [
                'success' => true,
                'status' => 300,
                'message' => 'Data gagal disimpan'
            ];
        }
    }

    public function getStockopnameDetail() {
        $user = Session::get('user');
        $branch = $user['user']['branch'];
        $membercode = $user['user']['fc_membercode'];
        $userid = $user['user']['userid'];
    
        $stockopname_detail = StockOpnameDetail::with('invstore.stock')->where('fc_stockopname_no', $userid)
                                    ->where('fc_membercode', $membercode)
                                    ->get();
    
        if ($stockopname_detail->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'data' => []
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Data ditemukan',
                'data' => $stockopname_detail,
            ], 200);
        }
    }


    public function deleteStockOpnameDetail($rownum){
        $user = Session::get('user');
        $branch = $user['user']['branch'];
        $membercode = $user['user']['fc_membercode'];
        $userid = $user['user']['userid'];

        $stockopname_detail = StockOpnameDetail::where('fc_stockopname_no', $userid)
                                ->where('fc_membercode', $membercode)
                                ->where('fn_rownum', $rownum)
                                ->delete();

        if($stockopname_detail){
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus',
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Data gagal dihapus',
            ], 400);
        }
    }

    public function submit_stockopname(){
        $prefix = 'STPM';
        $table = 't_stockopnamemst';
        $field = 'fc_stockopname_no';
        $length = 6;
        $delimiter = '/';
    
        $docNumber = DocNumber::generate_doc_number($prefix, $table, $field, $length, $delimiter);
        try {
            $user = Session::get('user');
            $branch = $user['user']['branch'];
            $membercode = $user['user']['fc_membercode'];
            $userid = $user['user']['userid'];
    
            // Nonaktifkan sementara pembatasan kunci asing
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
    
            DB::beginTransaction();
    
            // First, update the master table
            StockOpnameMaster::where('fc_stockopname_no', $userid)
                ->where('fc_membercode', $membercode)
                ->where('fc_branch', $branch)->update([
                    'fc_stockopname_no' => $docNumber,
                    'fc_stockopname_status' => 'F',
                    'fd_stockopname_end' => Carbon::now()->toDateTimeString()
                ]);
    
            // Then update the details table
            StockOpnameDetail::where('fc_stockopname_no', $userid)
                ->where('fc_membercode', $membercode)
                ->where('fc_branch', $branch)->update([
                    'fc_stockopname_no' => $docNumber,
                ]);
    
            DB::commit();
    
            // Aktifkan kembali pembatasan kunci asing
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
    
            // Retrieve the master record with its related details
            $stockOpnameMaster = StockOpnameMaster::with('stockopnamedetail')
                ->where('fc_stockopname_no', $docNumber)
                ->first();
    
            return response()->json([
                'success' => true,
                'message' => 'Stock opname submitted successfully',
                'data' => $stockOpnameMaster
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
    
            // Aktifkan kembali pembatasan kunci asing jika terjadi kesalahan
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
    
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    
}
