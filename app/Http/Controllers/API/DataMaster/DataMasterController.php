<?php

namespace App\Http\Controllers\API\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\TransaksiType;
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
}
