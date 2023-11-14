<?php

namespace App\Http\Controllers\API\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;

class MasterStockController extends Controller
{
    public function getStock(){
        $stock = Stock::with(['branch', 'namepack', 'type_stock1', 'type_stock2'])->get();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil menampilkan data stock',
            'data' => $stock
        ], 200);
    }
}
