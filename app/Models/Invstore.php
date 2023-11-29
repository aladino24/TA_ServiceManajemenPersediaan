<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invstore extends Model
{
    use HasFactory;

    protected static $logAttributes = ["*"];

    protected $table = 't_invstore';
    protected $primaryKey = 'fc_barcode';
    public $incrementing = false;
    protected $guarded = ['type'];
}
