<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiType extends Model
{
    use HasFactory, SoftDeletes;

    protected static $logAttributes = ["*"];

    protected $table = 't_trxtype';
    protected $primaryKey = 'fc_kode';
    // protected $primaryKey = ['fc_trx', 'fc_kode'];
    public $incrementing = false;
    protected $fillable = ['fc_trx', 'fc_kode', 'fv_description', 'fc_action'];
    protected $appends = [];
}
