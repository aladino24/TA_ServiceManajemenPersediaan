<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameDetail extends Model
{
    use HasFactory,\Awobaz\Compoships\Compoships;

    protected static $logAttributes = ["*"];

    protected $table = 't_stockopnamedtl';
    
    protected $primaryKey = ['fn_rownum', 'fc_divisioncode', 'fc_branch', 'fc_stockopname_no', 'fc_barcode', 'fc_membercode'];
    public $incrementing = false;
    protected $guarded = [
        'created_at',
        'updated_at',
    ];

    // stockopnamemst
    public function stockopnamemst()
    {
        return $this->belongsTo(StockOpnameMaster::class, 'fc_membercode', 'fc_membercode')->where('fc_stockopname_no', 'fc_stockopname_no');
    }

    // invstore
    public function invstore(){
        return $this->belongsTo(Invstore::class, ['fc_barcode','fc_membercode'], ['fc_barcode','fc_membercode']);
    }

}
