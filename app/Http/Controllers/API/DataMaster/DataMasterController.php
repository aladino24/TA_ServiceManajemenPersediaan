<?php

namespace App\Http\Controllers\API\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\TransaksiType;
use App\Models\Brand;
use Illuminate\Http\Request;

class DataMasterController extends Controller
{
    public function getUnity(){
        $data = TransaksiType::get();
        // api formatter
        return response()->json([
            'success' => true,
            'message' => 'Berhasil menampilkan data unity',
            'data' => $data,
        ], 200);
    }

    public function getGroupByBrand(Request $request){
        $data = Brand::select('fc_group')->where('fc_brand', $request->fc_brand)->groupBy('fc_group')->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Berhasil menampilkan data group by brand',
            'data' => $data,
        ], 200);
    }
}
