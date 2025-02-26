<?php

namespace App\Http\Controllers;

use App\Models\ClassSection;
use App\Models\Students;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixingController extends Controller
{
    
    public function deleteStudentsInClassSection(): void {
        
        $classSection = ClassSection::find(398)->id;

        // Fetch all the students that belong to that class section
        $students = Students::owner()->whereHas('class_section.class', function ($query) {
            $query->activeMediumOnly();
        })->whereHas('studentSessions', function ($query) use ($classSection) {
            $query->where('class_section_id', $classSection)
                ->where('session_year_id', getSettings('session_year')['session_year']);
        })->get();

        $count = $students->count();

        dd($count);

        // Iterate through each student and delete them
        $studentController = new StudentController();


        foreach ($students as $student) {
            $studentController->destroy($student->user_id);
        }

        dd($count . ' students deleted.');
    }

    public function deleteUseless(): array
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Get all students without a corresponding studentSession
            $studentsToDelete = Students::query()->wheredoesntHave('studentSessions')->get();

            // Store the count before deletion
            $deleteCount = $studentsToDelete->count();

            echo $deleteCount;

            // Log the students that will be deleted
            if ($deleteCount > 0) {
                Log::info('Deleting ' . $deleteCount . ' students without sessions. Student IDs: ' .
                    $studentsToDelete->pluck('id')->implode(', '));

                // Perform the deletion
                Students::query()->wheredoesntHave('studentSessions')->delete();

                DB::commit();

                return [
                    'status' => 'success',
                    'message' => $deleteCount . ' students without sessions were successfully deleted',
                    'deleted_count' => $deleteCount
                ];
            }

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'No students found without sessions',
                'deleted_count' => 0
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting students without sessions: ' . $e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Failed to delete students: ' . $e->getMessage(),
                'deleted_count' => 0
            ];
        }
    }


}
