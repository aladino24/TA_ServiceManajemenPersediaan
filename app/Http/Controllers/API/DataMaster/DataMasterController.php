<?php

namespace App\Http\Controllers\API\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\TransaksiType;
use App\Models\Brand;
use Illuminate\Http\Request;

class DataMasterController extends Controller
{
    // template
    public function get_data_where_field_id_get($model, $where_field, $id){
        $model = 'App\\Models\\' . $model;
        $data = $model::where($where_field, $id)->get();

        return ApiFormatter::getResponse($data);
    }

    public function getBrand(Request $request){
        $data = Brand::select('fc_brand')->groupBy('fc_brand')->get();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil menampilkan data brand',
            'data' => $data,
        ], 200);
    }
    
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

    public function getSubgroupByGroup(Request $request){
        $data = Brand::select('fc_subgroup')->where('fc_group', $request->fc_group)->groupBy('fc_subgroup')->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menampilkan data subgroup by group',
            'data' => $data
        ], 200);
    }
}
