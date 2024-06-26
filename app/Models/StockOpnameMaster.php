<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;

class StockOpnameMaster extends Model
{
    use HasFactory, Compoships;

    protected $table = 't_stockopnamemst';

    protected $primaryKey = ['fc_divisioncode', 'fc_branch', 'fc_stockopname_no', 'fc_membercode'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function stockopnamedetail()
    {
        return $this->hasMany(StockOpnameDetail::class, ['fc_membercode', 'fc_stockopname_no'], ['fc_membercode', 'fc_stockopname_no']);
    }

    public function ascustomer()
    {
        return $this->belongsTo(AsCustomer::class, 'fc_membercode', 'fc_membercode');
    }
}
