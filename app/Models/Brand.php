<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 't_brand';
    protected $primaryKey = 'id';
    // protected $primaryKey = ['fc_divisioncode', 'fc_branch', 'fc_brand', 'fc_group', 'fc_subgroup'];
    public $incrementing = true;
    protected $guarded = ['id'];
    protected $appends = [];

    public function branch(){
        return $this->belongsTo(TransaksiType::class, 'fc_branch', 'fc_kode')->withTrashed();
    }
}
