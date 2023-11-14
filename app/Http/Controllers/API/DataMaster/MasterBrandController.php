<?php

namespace App\Http\Controllers\API\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MasterBrandController extends Controller
{
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
}
