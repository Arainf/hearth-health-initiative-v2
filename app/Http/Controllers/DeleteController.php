<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Records;
use Illuminate\Support\Facades\DB;

class DeleteController extends Controller
{
    public function index()
    {

    }

// error
    public function deletePatient($id){
        DB::beginTransaction();
        
        try {
            $patient = Patient::findOrFail($id);
            
            // Archive all records for this patient using DB facade to ensure it works
            DB::table('records')
                ->where('patient_id', $patient->id)
                ->update(['is_archived' => true]);
            
            // Delete the patient
            $patient->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Patient and records archived successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to delete patient: ' . $e->getMessage()
            ], 500);
        }
    }
}
