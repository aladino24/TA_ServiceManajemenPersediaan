<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsCustomer extends Model
{
    use HasFactory;

    protected $table = 't_customer';
    protected $primaryKey = 'fc_membercode';
    public $incrementing = false;
    protected $guarded = ['type'];
    protected $appends = [];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // StockOpnameMaster
    public function stockopnamemaster()
    {
        return $this->hasMany(StockOpnameMaster::class, 'fc_membercode', 'fc_membercode');
    }
}
