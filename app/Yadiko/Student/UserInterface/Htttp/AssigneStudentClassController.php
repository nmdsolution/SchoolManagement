<?php

namespace App\Yadiko\Student\UserInterface\Htttp;

use App\Models\Attendance;
use Exception;
use App\Models\Students;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use App\Models\StudentSessions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Yadiko\Student\Application\DTO\BaseStudentHelper;

class AssigneStudentClassController extends BaseStudentHelper
{
    public function assignClass()
    {
        try {
            $class_sections = ClassSection::with(['class.stream', 'section', 'class.medium'])
                ->whereHas('class', function ($q) {
                    $q->where('center_id', Auth::user()->center->id);
                })
                ->get();

            return view('students.assign-class', compact('class_sections'));
        } catch (Exception $e) {
            Log::error('Error fetching class sections: ' . $e->getMessage());
            return back()->with('error', 'Unable to fetch class sections.');
        }
    }

    public function assignClass_store(Request $request)
    {
        set_time_limit(10000);
        
        // Step 1: Initial Validation
        if (!$this->validateUserAccess()) {
            return response()->json(['error' => true, 'message' => 'Unauthorized access']);
        }

        if (!$this->validateRequest($request)) {
            return response()->json(['error' => true, 'message' => 'Invalid request data']);
        }

        // Step 2: Process Transfer
        try {
            return $this->processTransfer($request);
        } catch (Exception $e) {
            Log::error('Global Transfer Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Transfer process failed: ' . $e->getMessage()
            ]);
        }
    }

    private function validateUserAccess(): bool
    {
        return Auth::user()->hasRole('Center') || Auth::user()->hasRole('Super Admin');
    }

    private function validateRequest(Request $request): bool
    {
        $validator = Validator::make($request->all(), [
            'from_class_section_id' => 'required|exists:class_sections,id',
            'to_class_section_id' => 'required|exists:class_sections,id|different:from_class_section_id',
            'selected_id' => 'required|string'
        ]);

        return !$validator->fails();
    }

    private function processTransfer(Request $request)
    {
        DB::beginTransaction();
        try {
            $selectedStudents = array_filter(explode(',', $request->selected_id));
            $sessionYearId = getSettings('session_year')['session_year'];
            $results = $this->initializeResults();

            // Verify class sections and fetch students
            [$fromClass, $toClass] = $this->verifyClassSections($request);
            $validStudents = $this->fetchValidStudents($selectedStudents);

            // Process each student
            foreach ($selectedStudents as $studentId) {
                try {
                    $this->processStudentTransfer($studentId, $validStudents, $fromClass, $toClass, $sessionYearId, $results);
                } catch (Exception $e) {
                    $this->handleTransferError($e, $studentId, $validStudents, $results);
                }
            }

            // Finalize transaction
            if ($results['success'] > 0) {
                DB::commit();
            } else {
                DB::rollBack();
            }

            return $this->prepareTransferResponse($results, $selectedStudents);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function initializeResults(): array
    {
        return [
            'success' => 0,
            'failed' => 0,
            'messages' => [],
            'errors' => []
        ];
    }

    private function verifyClassSections(Request $request): array
    {
        $fromClass = ClassSection::with('class')->findOrFail($request->from_class_section_id);
        $toClass = ClassSection::with('class')->findOrFail($request->to_class_section_id);

        if (Auth::user()->hasRole('Center')) {
            if ($fromClass->class->center_id !== Auth::user()->center->id ||
                $toClass->class->center_id !== Auth::user()->center->id) {
                throw new Exception('Invalid class section selection for this center');
            }
        }

        return [$fromClass, $toClass];
    }

    private function fetchValidStudents(array $selectedStudents)
    {
        return Students::whereIn('id', $selectedStudents)
            ->where('center_id', Auth::user()->center->id)
            ->with(['user:id,first_name,last_name'])
            ->get()
            ->keyBy('id');
    }

    private function transferStudentData($student, $fromClassSectionId, $toClassSectionId, $sessionYearId)
    {
        try {
            // Update main student record
            Students::where('id', $student->id)
                ->update([
                    'class_section_id' => $toClassSectionId,
                    'updated_at' => now()
                ]);

            // Update student session
            $this->updateStudentSession($student->id, $toClassSectionId, $sessionYearId);

            // Update related records
            $this->updateRelatedRecords($student->id, $fromClassSectionId, $toClassSectionId, $sessionYearId);

            // Transfer exam records
            $this->transferExamRecords($student->id, $fromClassSectionId, $toClassSectionId, $sessionYearId);

            // Update annual records
            $this->updateAnnualRecords($student->id, $fromClassSectionId, $toClassSectionId);

            return true;
        } catch (Exception $e) {
            throw new Exception("Error transferring student data: " . $e->getMessage());
        }
    }

    private function updateStudentSession($studentId, $toClassSectionId, $sessionYearId)
    {
        StudentSessions::where([
            'student_id' => $studentId,
            'session_year_id' => $sessionYearId,
            'active' => 1
        ])->update(['active' => 0]);

        StudentSessions::create([
            'student_id' => $studentId,
            'session_year_id' => $sessionYearId,
            'class_section_id' => $toClassSectionId,
            'transfer_date' => now(),
            'active' => 1
        ]);
    }

    private function updateRelatedRecords($studentId, $fromClassSectionId, $toClassSectionId, $sessionYearId)
    {
        // Update student subjects
        DB::table('student_subjects')
            ->where([
                'student_id' => $studentId,
                'class_section_id' => $fromClassSectionId,
                'session_year_id' => $sessionYearId
            ])
            ->update([
                'class_section_id' => $toClassSectionId,
                'updated_at' => now()
            ]);

        // Update attendance records
        DB::table('student_attendances')
            ->where([
                'student_id' => $studentId,
                'class_section_id' => $fromClassSectionId,
                'session_year_id' => $sessionYearId
            ])
            ->update([
                'class_section_id' => $toClassSectionId,
                'updated_at' => now()
            ]);
    }

    private function transferExamRecords($studentId, $fromClassSectionId, $toClassSectionId, $sessionYearId)
    {
        // Update exam results
        DB::table('exam_results')
            ->where([
                'student_id' => $studentId,
                'class_section_id' => $fromClassSectionId,
                'session_year_id' => $sessionYearId
            ])
            ->update([
                'class_section_id' => $toClassSectionId,
                'updated_at' => now()
            ]);

        // Update exam reports
        DB::table('exam_reports')
            ->where([
                'student_id' => $studentId,
                'class_section_id' => $fromClassSectionId,
                'session_year_id' => $sessionYearId
            ])
            ->update([
                'class_section_id' => $toClassSectionId,
                'updated_at' => now()
            ]);

        // Update exam marks with soft delete and new record creation
        $this->transferExamMarks($studentId, $fromClassSectionId, $toClassSectionId, $sessionYearId);
    }

    private function transferExamMarks($studentId, $fromClassSectionId, $toClassSectionId, $sessionYearId)
    {
        $examMarks = DB::table('exam_marks as em')
            ->join('exam_timetables as et', 'em.exam_timetable_id', '=', 'et.id')
            ->select('em.*', 'et.exam_id', 'et.subject_id')
            ->where([
                'em.student_id' => $studentId,
                'em.session_year_id' => $sessionYearId,
                'et.class_section_id' => $fromClassSectionId
            ])
            ->whereNull('em.deleted_at')
            ->get();

        foreach ($examMarks as $mark) {
            // Find new timetable
            $newTimetable = DB::table('exam_timetables')
                ->where([
                    'class_section_id' => $toClassSectionId,
                    'exam_id' => $mark->exam_id,
                    'subject_id' => $mark->subject_id,
                    'session_year_id' => $sessionYearId
                ])
                ->first();

            if ($newTimetable) {
                // Soft delete old record
                DB::table('exam_marks')
                    ->where('id', $mark->id)
                    ->update([
                        'deleted_at' => now(),
                        'updated_at' => now()
                    ]);

                // Create new record
                DB::table('exam_marks')->insert([
                    'exam_timetable_id' => $newTimetable->id,
                    'student_id' => $studentId,
                    'subject_id' => $mark->subject_id,
                    'obtained_marks' => $mark->obtained_marks,
                    'total_marks' => $mark->total_marks,
                    'passing_marks' => $mark->passing_marks,
                    'teacher_review' => $mark->teacher_review,
                    'passing_status' => $mark->passing_status,
                    'session_year_id' => $sessionYearId,
                    'grade' => $mark->grade,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    private function updateAnnualRecords($studentId, $fromClassSectionId, $toClassSectionId)
    {
        $tables = [
            'annual_class_details',
            'annual_subject_reports'
        ];

        foreach ($tables as $table) {
            DB::table($table)
                ->where([
                    'student_id' => $studentId,
                    'class_section_id' => $fromClassSectionId
                ])
                ->update([
                    'class_section_id' => $toClassSectionId,
                    'updated_at' => now()
                ]);
        }
    }

    public function transferStudentList(Request $request)
    {
        try {
            $fromClassSectionId = $request->from_class_section_id;
            $sessionYearId = getSettings('session_year')['session_year'];

            $students = Students::with([
                'user:id,first_name,last_name,image',
                'studentSessions' => function ($query) use ($sessionYearId) {
                    $query->where('session_year_id', $sessionYearId);
                },
                'class_section.class.stream',
                'class_section.section'
            ])
                ->whereHas('studentSessions', function ($query) use ($fromClassSectionId, $sessionYearId) {
                    $query->where('session_year_id', $sessionYearId)
                        ->where('class_section_id', $fromClassSectionId);
                });

            if (Auth::user()->hasRole('Center')) {
                $students->where('center_id', Auth::user()->center->id);
            }

            if ($request->search) {
                $search = $request->search;
                $students->where(function($query) use ($search) {
                    $query->whereHas('user', function ($query) use ($search) {
                        $query->where('first_name', 'LIKE', "%$search%")
                            ->orWhere('last_name', 'LIKE', "%$search%");
                    })
                        ->orWhere('admission_no', 'LIKE', "%$search%");
                });
            }

            $students = $students->get();

            return response()->json([
                'total' => $students->count(),
                'rows' => $students->map(function ($student) {
                    return [
                        'chk' => '<input type="checkbox" class="assign_student" name="assign_student" value="' . $student->id . '">',
                        'id' => $student->id,
                        'admission_no' => $student->admission_no,
                        'user_id' => $student->user->id,
                        'first_name' => $student->user->first_name,
                        'last_name' => $student->user->last_name,
                        'image' => $student->user->image,
                        'current_class' => $student->class_section->full_name,
                        'roll_number' => $student->roll_number
                    ];
                })
            ]);

        } catch (Exception $e) {
            Log::error('Student List Error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => true, 'message' => 'Error fetching student list: ' . $e->getMessage()]);
        }
    }
}