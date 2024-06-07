<?php

namespace App\Http\Controllers\API\Apps;

use App\Http\Controllers\Controller;
use App\Models\Invstore;
use App\Models\StockOpnameMaster;
use App\Models\StockOpnameDetail;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{

    public function index(){
        // session
        $user = Session::get('user');
        $userid = $user['user']['userid'];
        $branch = $user['user']['branch'];
        $divisioncode = $user['user']['divisioncode'];
        $membercode = $user['user']['fc_membercode'];

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
        

        if(!empty($stockopname_master)){
         
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
}
