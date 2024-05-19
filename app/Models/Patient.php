<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\PatientCreated;
use Illuminate\Support\Facades\DB;

class Patient extends Model
{
    use HasFactory;

    protected $table = 't_patient';
    protected $primaryKey = 'fc_patient_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'fc_patient_id', 
        'fc_patient_name', 
        'fc_patient_gender', 
        'fn_patient_age', 
        'fc_patient_address', 
        'fc_patient_phone'
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::created(function ($patient) {
    //         DB::transaction(function () use ($patient) {
    //             event(new PatientCreated($patient));
    //         });
    //     });
    // }

    public function usageMasters()
    {
        return $this->hasMany(UsageMaster::class, 'fc_patient_id', 'fc_patient_id');
    }

}
