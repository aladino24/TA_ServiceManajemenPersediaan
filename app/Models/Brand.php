<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Blameable;
use App\Traits\CompositeKey;

class Brand extends Model
{
    use HasFactory,SoftDeletes;

    protected static $logAttributes = ["*"];

    protected $table = 't_brand';
    protected $primaryKey = 'id';
    // protected $primaryKey = ['fc_divisioncode', 'fc_branch', 'fc_brand', 'fc_group', 'fc_subgroup'];
    public $incrementing = false;
    protected $guarded = ['type'];
    protected $appends = [];

    public function branch(){
        return $this->belongsTo(TransaksiType::class, 'fc_branch', 'fc_kode')->withTrashed();
    }
}
