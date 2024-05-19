<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\PatientCreated;
use App\Models\UsageMaster;
use Illuminate\Support\Facades\Session;
use DB;

class CreateUsageMasterForPatient
{
    /**
     * Create the event listener.
     */
    public function __construct(PatientCreated $event)
    {
        //
    }

    private function generateDocumentNumber(){
        $today = new \DateTime();
        $month = str_pad($today->format('m'), 2, '0', STR_PAD_LEFT);
        $year = substr($today->format('Y'), -2);
        $randomChars = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        
        return "USAGE/{$month}/{$year}/{$randomChars}";
    }

    /**
     * Handle the event.
     */
    public function handle(PatientCreated $event)
    {
        $user = Session::get('user');
        $divisioncode = $user['user']['divisioncode'];
        $branch = $user['user']['branch'];
        $userid = $user['user']['userid'];
        $patient = $event->patient;
        $request = $event->request;

        DB::transaction(function () use ($patient, $divisioncode, $branch, $userid, $request) {
            UsageMaster::create([
                'fc_divisioncode' => $divisioncode,  
                'fc_branch' => $branch,      
                'fc_usageno' => $this->generateDocumentNumber(),    
                'fc_patient_id' => $patient->fc_patient_id,
                'fd_usage_date' => now(),             
                'fc_admin' =>  $userid,
                'fv_description' => $request->fv_description            
            ]);
        });
        // dd($event->request->fv_description);
    }
}
