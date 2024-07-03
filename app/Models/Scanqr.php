<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScanQr extends Model
{
    use HasFactory;
    protected static $logAttributes = ["*"];

    protected $table = 't_scanqr';
    protected $primaryKey = 'fc_scanqrno';
    public $incrementing = false;
    protected $guarded = ['type'];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];


    public function invstore(){
        return $this->hasOne(Invstore::class, 'fc_barcode', 'fc_barcode');
    }
}
