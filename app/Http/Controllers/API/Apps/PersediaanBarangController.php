<?php

namespace App\Http\Controllers\API\Apps;

use App\Http\Controllers\Controller;
use App\Models\Invstore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use DB;

class PersediaanBarangController extends Controller
{
    public function datatables_detail(){
        $user = Session::get('user');
        $branch = $user['user']['branch'];
        $data = Invstore::with('stock')
            ->select('fc_stockcode', DB::raw('SUM(fn_quantity) as fn_quantity'))
            ->where('fc_branch', $branch)
            ->groupBy('fc_stockcode')
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }


    public function datatables_detail_inventory($fc_stockcode)
    {
        $user = Session::get('user');
        $branch = $user['user']['branch'];
        $decode_fc_stockcode = base64_decode($fc_stockcode);
        $data = Invstore::with('stock')->where('fc_stockcode', $decode_fc_stockcode)->where('fc_branch', $branch)->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }
}
