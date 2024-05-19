<?php

namespace App\Http\Controllers\API\Apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\UsageMaster;

class PemakaianBarangController extends Controller
{
    public function getStatusUsageMaster(){
        $user = Session::get('user');
        $userid = $user['user']['userid'];

        $data = UsageMaster::where('fc_admin', $userid)->first();
        if($data){
            return response()->json([
                'success' => true,
                'message' => 'Data ditemukan',
                'data' => $data,
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }
    }
}
