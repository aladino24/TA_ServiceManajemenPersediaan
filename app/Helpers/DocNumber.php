<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DocNumber {
    
    public static function generate_doc_number($prefix, $table, $field, $length, $delimiter = '/') {
        $currentMonth = date('m');
        $currentYear = date('y'); // Last two digits of the year
        $fullPrefix = $prefix . $delimiter . $currentMonth . $delimiter . $currentYear . $delimiter;
        $lastRecord = DB::table($table)->where($field, 'like', $fullPrefix . '%')->orderBy($field, 'desc')->first();
        if ($lastRecord) {
            $lastNumber = (int)substr($lastRecord->$field, strrpos($lastRecord->$field, $delimiter) + 1);
        } else {
            $lastNumber = 0;
        }
        // Increment the number
        $newNumber = $lastNumber + 1;

        $paddedNumber = str_pad($newNumber, $length, '0', STR_PAD_LEFT);

        return $fullPrefix . $paddedNumber;
    }
}

?>
