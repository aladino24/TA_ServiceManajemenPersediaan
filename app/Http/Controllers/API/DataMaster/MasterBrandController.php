<?php

namespace App\Http\Controllers\API\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MasterBrandController extends Controller
{
    public function datatables(){
        $user = Session::get('user');
        $branch = $user['user']['branch'];
        $data = Brand::with('branch')
                ->where('fc_branch', $branch)
                ->orderBy('created_at', 'DESC')->get();

        return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
    }

    public function getBrand(){
        $user = Session::get('user');
        if ($user) {
            // Akses informasi cabang pengguna dari objek user
            $branch = $user['user']['branch'];

            // Logika yang sudah ada
            $data = Brand::with('branch')->orderBy('created_at', 'DESC')
                            ->where('fc_branch', $branch)
                            ->get();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menampilkan data brand',
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }

    public function createBrand(Request $request){
        $validator = Validator::make($request->all(), [
            'fc_divisioncode' => 'required',
            'fc_brand' => 'required',
            'fc_group' => 'required',
            'fc_subgroup' => 'required',
        ]);

        if($validator->fails()) {
            return [
                'status' => 300,
                'message' => $validator->errors()->first()
            ];
        }

        $user = Session::get('user');
        $branch = $user['user']['branch'];
        $name = $user['user']['username'];

        $cek_data = Brand::where([
            'fc_divisioncode' => $request->fc_divisioncode,
            'fc_branch' => $branch,
            'fc_brand' => $request->fc_brand,
            'fc_group' => $request->fc_group,
            'fc_subgroup' => $request->fc_subgroup,
            'deleted_at' => null,
        ])->withTrashed()->count();
        
        if($cek_data > 0){
            return [
                'status' => 300,
                'message' => 'Oops! Insert gagal karena data sudah ditemukan didalam sistem kami'
            ];
        }

        // try catch insert
        try {
            $brand = Brand::create([
                'fc_divisioncode' => $request->fc_divisioncode,
                'fc_branch' => $branch,
                'fc_brand' => $request->fc_brand,
                'fc_group' => $request->fc_group,
                'fc_subgroup' => $request->fc_subgroup,
                'created_by' => $name,
                'updated_by' => $name,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Berhasil menambahkan data brand',
                'data' => $brand
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Oops! Terjadi kesalahan saat menambahkan data brand',
                'data' => $th->getMessage()
            ], 500);
        }
    }


    public function updateBrand(Request $request){
        // Validasi input
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'fc_divisioncode' => 'required',
            'fc_brand' => 'required',
            'fc_group' => 'required',
            'fc_subgroup' => 'required',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 300,
                'success' => false,
                'data' => $validator->errors(),
                'message' => $validator->errors()->first()
            ];
        }

        $user = Session::get('user');
        $branch = $user['user']['branch'];
        $username = $user['user']['username'];

        try {
            DB::beginTransaction();

            // Cek apakah data sudah ada
            $cek_data = Brand::where([
                'fc_divisioncode' => $request->fc_divisioncode,
                'fc_branch' => $branch,
                'fc_brand' => $request->fc_brand,
                'fc_group' => $request->fc_group,
                'fc_subgroup' => $request->fc_subgroup,
                'deleted_at' => null,
            ])->withTrashed()->count();

            if ($cek_data > 0) {
                DB::rollBack();

                $response = [
                    'status' => 300,
                    'success' => false,
                    'data' => $cek_data,
                    'message' => 'Oops! Insert gagal karena data sudah ditemukan didalam sistem kami'
                ];

                return response()->json($response, 300);
            }

            // Update data brand
            $brand = Brand::where([
                'id' => $request->id,
            ])->update([
                'fc_divisioncode' => $request->fc_divisioncode,
                'fc_branch' => $branch,
                'fc_brand' => $request->fc_brand,
                'fc_group' => $request->fc_group,
                'fc_subgroup' => $request->fc_subgroup,
                'updated_by' => $username,
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Berhasil mengubah data brand',
                'data' => $brand
            ], 200);
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => 'Oops! Terjadi kesalahan saat mengubah data brand',
                'data' => $th->getMessage()
            ], 500);
        }
    }

    public function deleteBrand($id){
        $user = Session::get('user');
        $username = $user['user']['username'];

        try{
            $brand = Brand::find($id);

            if (!$brand) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Brand not found',
                ], 404);
            }
    
            $brand->update([
                'deleted_by' => $username
            ]);
    
            $brand->delete();
    
            return response()->json([
                'status' => 200,
                'message' => 'Berhasil menghapus data brand',
                'data' => $brand
            ], 200);
        }catch(\Throwable $th){
            return response()->json([
                'status' => 500,
                'message' => 'Oops! Terjadi kesalahan saat menghapus data brand',
                'data' => $th->getMessage()
            ], 500);
        }
    }
}
