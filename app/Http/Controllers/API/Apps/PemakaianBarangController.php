<?php

namespace App\Http\Controllers\API\Apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\UsageMaster;
use App\Models\Patient;
use App\Models\Invstore;

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
        $data = Invstore::where('fc_barcode', $fc_barcode)->first();
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
}
