<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageMaster extends Model
{
    use HasFactory;

    protected $table = 't_usage_master';
    protected $primaryKey = 'fi_usage_id';

    protected $fillable = [
        'fc_divisioncode',
        'fc_branch',
        'fc_usageno',
        'fc_patient_id',
        'fd_usage_date',
        'fc_doctor_id',
        'fc_doctor_name',
        'fc_admin',
        'fv_description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($usage) {
            DB::transaction(function () use ($usage) {
                $usage->usageDetails()->delete();
            });
        });
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'fc_patient_id', 'fc_patient_id');
    }

    public function usageDetails()
    {
        return $this->hasMany(UsageDetail::class, 'fi_usage_id', 'fi_usage_id');
    }
}
