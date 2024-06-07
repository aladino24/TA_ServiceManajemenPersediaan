<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Stock;

class Invstore extends Model
{
    use HasFactory;

    protected static $logAttributes = ["*"];

    protected $table = 't_invstore';
    protected $primaryKey = 'fc_barcode';
    public $incrementing = false;
    protected $guarded = ['type'];

    // hidden
    protected $hidden = [
        'created_at',
        'updated_at',
    ];



    public function stock()
    {
        return $this->belongsTo(Stock::class, 'fc_stockcode', 'fc_stockcode')->withTrashed();
    }

    public function usageDetail()
    {
        return $this->hasMany(UsageDetail::class, 'fc_barcode', 'fc_barcode');
    }
    

    
}
