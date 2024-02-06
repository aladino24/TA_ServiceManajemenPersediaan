<?php

namespace App\Http\Controllers\API\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\BankAcc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MasterBankAccController extends Controller
{
    public function getBankAcc(){
        // response datatable

        $bank_acc = BankAcc::with(['branch'])->get();

        return DataTables::of($bank_acc)
                ->addIndexColumn()
                ->make(true);
    }


    public function createBankAcc(Request $request){
        $validator = Validator::make($request->all(), [
            'fc_branch' => 'required',
            'fc_divisioncode' => 'required',
            'fv_bankname' => 'required',
            'fc_bankcode' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data bank account',
                'errors' => $validator->errors(),
            ], 400);
        }

        $cek_data = BankAcc::where([
            'fc_branch' => $request->fc_branch,
            'fc_divisioncode' => $request->fc_divisioncode,
            'fv_bankname' => $request->fv_bankname,
            'fc_bankcode' => $request->fc_bankcode,
            'deleted_at' => null
        ])->withTrashed()->count();

        if ($cek_data > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Data bank account sudah ada',
            ], 400);
        }

        try {
            $bankacc = BankAcc::create([
                'fc_divisioncode' => $request->fc_divisioncode,
                'fc_branch' => $request->fc_branch,
                'fv_bankname' => $request->fv_bankname,
                'fc_bankcode' => $request->fc_bankcode,
                'fc_banktype' => $request->fc_banktype,
                'fv_bankbranch' => $request->fv_bankbranch,
                'fv_bankusername' => $request->fv_bankusername,
                'fv_bankaddress1' => $request->fv_bankaddress1,
                'fv_bankaddress2' => $request->fv_bankaddress2,
                'fl_bankhold' => $request->fl_bankhold,
            ]);

            if($bankacc){
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil menambahkan data bank account',
                    'data' => $bankacc
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data bank account',
                'errors' => $th->getMessage(),
            ], 500);
        }

    }
}
