<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageDetail extends Model
{
    use HasFactory;

    protected $table = 't_usage_detail';
    protected $primaryKey = 'fi_detail_id';

    protected $fillable = [
        'fi_usage_id',
        'fc_stockcode',
        'fc_barcode',
        'fn_quantity_used',
    ];

    public function usageMaster()
    {
        return $this->belongsTo(UsageMaster::class, 'fi_usage_id', 'fi_usage_id');
    }
}
