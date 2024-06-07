<?php

namespace App\Http\Controllers\API\Apps;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\UsageMaster;
use App\Models\Patient;
use App\Models\Invstore;
use App\Models\UsageDetail;
use App\Models\ScanQr;
use Carbon\Carbon;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class PemakaianBarangController extends Controller
{
    public function getStatusUsageMaster(){
        $user = Session::get('user');
        $userid = $user['user']['userid'];

        $data = UsageMaster::with('patient')->where('fc_admin', $userid)->first();
        if($data){
            return response()->json([
                'success' => true,
                'message' => 'Data ditemukan',
                'data' => $data,
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 201);
        }
    }

    public function deleteUsageMaster($fc_patient_id){
        $patient = Patient::find($fc_patient_id);
        if ($patient) {
            $patient->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus',
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }
    }

    public function getDetailBarang($fc_barcode){
        // decode
        $fc_barcode = base64_decode($fc_barcode);
        $data = Invstore::with('stock')->where('fc_barcode', $fc_barcode)->first();
        if($data){
            return response()->json([
                'success' => true,
                'message' => 'Data ditemukan',
                'data' => $data,
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 201);
        }
    }

    public function getUsageDetail($fi_usage_id){
        $user = Session::get('user');
        $divisioncode = $user['user']['divisioncode'];
        $branch = $user['user']['branch'];

        $data = UsageDetail::with('invstore.stock')
                ->where('fi_usage_id', $fi_usage_id)
                ->where('fc_divisioncode', $divisioncode)
                ->where('fc_branch', $branch)
                ->get();
        
        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function createUsageDetail(Request $request){
        // Validation of the request
        $validator = Validator::make($request->all(), [
            'fi_usage_id' => 'required',
            'fc_stockcode' => 'required',
            'fc_barcode' => 'required',
            'fn_quantity_used' => 'required',
        ]);

        $user = Session::get('user');
        $userid = $user['user']['userid'];
        $divisioncode = $user['user']['divisioncode'];
        $branch = $user['user']['branch'];

        try {

            $sum_quantity = Invstore::where('fc_barcode', $request->fc_barcode)
            ->where('fc_branch', $branch)
            ->where('fc_divisioncode', $divisioncode)
            ->sum('fn_quantity');

            $quantity_usagedetail = UsageDetail::where('fc_branch', $branch)
            ->where('fc_divisioncode', $divisioncode)
            ->where('fc_barcode', $request->fc_barcode)
            ->sum('fn_quantity_used');

            if(($quantity_usagedetail + $request->fn_quantity_used) > $sum_quantity){
                // lempar ke catch
                throw new \Exception('Jumlah pemakaian melebihi stok');
            }
            // dd($sum_quantity);


            DB::beginTransaction();

            $data = UsageDetail::create([
                'fi_usage_id' => $request->fi_usage_id,
                'divisioncode' => $divisioncode,
                'branch' => $branch,
                'fc_stockcode' => $request->fc_stockcode,
                'fc_barcode' => $request->fc_barcode,
                'fn_quantity_used' => $request->fn_quantity_used,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Detail barang berhasil ditambahkan.',
                'data' => $data
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            
            // Returning the error response
            return response()->json([
                'success' => false,
                'error' => 'Gagal menambahkan detail barang.',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    private function generateDocumentNumber(){
        $today = new \DateTime();
        $month = str_pad($today->format('m'), 2, '0', STR_PAD_LEFT);
        $year = substr($today->format('Y'), -2);
        $randomChars = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        
        return "USAGE/{$month}/{$year}/{$randomChars}";
    }


    public function insert_scanqr(Request $request){
        $validator = Validator::make($request->all(), [
            'fc_barcode' => 'required',
        ]);

        $user = Session::get('user');
        $userid = $user['user']['userid'];
        $divisioncode = $user['user']['divisioncode'];
        $branch = $user['user']['branch'];
        $membercode = $user['user']['fc_membercode'];

   

        if($validator->fails()) {
            return [
                'status' => 300,
                'message' => $validator->errors()->first()
            ];
        }

        DB::beginTransaction();
        // create data ScanQr
        try {
            $today = new \DateTime();
            $month = str_pad($today->format('m'), 2, '0', STR_PAD_LEFT);
            $year = substr($today->format('Y'), -2);
            $fc_scanqrno_prefix = "USAGE/{$month}/{$year}/";
            $data = ScanQr::create([
                'fc_divisioncode' => $divisioncode,
                'fc_branch' => $branch,
                'fc_scanqrno' => IdGenerator::generate(['table' => 't_scanqr', 'field' => 'fc_scanqrno', 'length' => 18, 'prefix' => $fc_scanqrno_prefix]),
                'fc_barcode' => $request->fc_barcode,
                'fc_warehousecode' => $request->fc_warehousecode,
                'fc_membercode' => $membercode,
                'fc_userid' => $userid,
                'fd_scanqrdate' => Carbon::now(),
                'fc_scanqrstatus' => 'S',
                'fv_description' => $request->fv_description
            ]);
            // dd($data);
            DB::commit();
            return [
                'success' => true,
				'status' => 201, // SUCCESS
                'link' => '/',
				'message' => 'Barang berhasil terpakai'
			];
        } catch (\Exception $e) {
            DB::rollback();

			return [
                'success' => false,
				'status' 	=> 300, // GAGAL
				'message'       => (env('APP_DEBUG', 'true') == 'true')? $e->getMessage() : 'Operation error'
			];
        }
    }

    public function getNewScanqr(){
        $user = Session::get('user');
        $divisioncode = $user['user']['divisioncode'];
        $branch = $user['user']['branch'];
        $userid = $user['user']['userid'];
        $membercode = $user['user']['fc_membercode'];

        $data = ScanQr::with('invstore.stock')
            ->where('fc_divisioncode', $divisioncode)
            ->where('fc_branch', $branch)
            ->where('fc_userid', $userid)
            ->where('fc_membercode', $membercode)
            ->orderBy('fd_scanqrdate', 'desc')
            ->first();

        if (!$data) {
            return DataTables::of([]) // Mengembalikan array kosong jika $data adalah null
                ->addIndexColumn()
                ->make(true);
        }

        return DataTables::of([$data])
            ->addIndexColumn()
            ->make(true);
    }


}
