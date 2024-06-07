<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameMaster extends Model
{
    use HasFactory;
    
    protected $table = 't_stockopnamemst';
    
    protected $primaryKey = ['fc_divisioncode', 'fc_branch', 'fc_stockopname_no', 'fc_membercode'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded=[
        'created_at',
        'updated_at',
    ];

    // stockopnamedetail
    public function stockopnamedetail()
    {
        return $this->hasMany(StockOpnameDetail::class, ['fc_membercode','fc_stockopname_no'], ['fc_membercode', 'fc_stockopname_no']);
    }
}
