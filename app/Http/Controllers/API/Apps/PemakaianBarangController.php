<?php

namespace App\Http\Controllers\API\Apps;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\UsageMaster;
use App\Models\Patient;
use App\Models\Invstore;
use App\Models\UsageDetail;

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

    public function createUsageDetail(Request $request){

        $validator = Validator::make($request->all(), [
            'fi_usage_id' => 'required',
            'fc_stockcode' => 'required',
            'fc_barcode' => 'required',
            'fn_quantity_used' => 'required',
        ]);


        $user = Session::get('user');
        $userid = $user['user']['userid'];

        try {
            DB::beginTransaction();
            UsageDetail::create([
                'fi_usage_id' => $request->fi_usage_id,
                'fc_stockcode' => $request->fc_stockcode,
                'fc_barcode' => $request->fc_barcode,
                'fn_quantity_used' => $request->fn_quantity_used,
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Gagal menambahkan detail barang.',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
