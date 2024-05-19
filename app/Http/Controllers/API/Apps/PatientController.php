<?php

namespace App\Http\Controllers\API\Apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Validator;
use App\Models\Patient;
use DB;

class PatientController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fc_patient_name' => 'required|string|max:100',
            'fc_patient_address' => 'required|string|max:255',
            'fc_patient_phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $patient = null;

            DB::transaction(function () use ($request, &$patient) {
                $patient = Patient::create([
                    'fc_patient_id' => IdGenerator::generate(['table' => 't_patient','field'=>'fc_patient_id', 'length' => 6, 'prefix' => date('y')]),
                    'fc_patient_name' => $request->fc_patient_name,
                    'fc_patient_gender' => $request->fc_patient_gender,
                    'fn_patient_age' => $request->fd_patient_age,
                    'fc_patient_address' => $request->fc_patient_address,
                    'fc_patient_phone' => $request->fc_patient_phone,
                ]);

                event(new \App\Events\PatientCreated($patient, $request));
            });

            return response()->json([
                'success' => true,
                'message' => 'Patient created successfully.',
                'data' => $patient,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal menambahkan data pemakai/pasien.',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
