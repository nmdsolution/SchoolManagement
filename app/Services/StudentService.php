<?php

namespace App\Services;

use App\Http\Forms\StudentAdmissionForm;
use App\Http\Requests\User\StoreStudentAdmissionRequest;
use App\Models\Student;
use App\Models\StudentAdmission;
use App\Models\StudentAdmissionStatus;
use App\Models\StudentExam;
use App\Models\StudentExamStatus;
use App\Models\Students;
use App\Models\StudentSequence;
use Illuminate\Support\Facades\Auth;

class StudentService
{
    public function __construct()
    {
    }

    public function store(StoreStudentAdmissionRequest $request)
    {
        if (!Auth::user()->can('student-create') || !Auth::user()->can('student-edit')) {
            return redirect()->route('home');
        }

        $student = Student::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'father_name' => $request->father_name,
            'father_phone' => $request->father_phone,
            'father_email' => $request->father_email,
            'mother_name' => $request->mother_name,
            'mother_phone' => $request->mother_phone,
            'mother_email' => $request->mother_email,
            'nationality' => $request->nationality,
            'dob' => $request->dob,
            'born_at' => $request->born_at,
            'class_section_id' => $request->class_section_id,
            'admission_date' => $request->admission_date,
            'status' => $request->status,
            'repeater' => $request->repeater,
            'image' => $request->image,
        ]);

        $student->save();        

        return redirect()->route('student.index');
    }

    public function update(Students $student, StudentAdmissionForm $request)
    {
        if (!Auth::user()->can('student-edit')) {
            return redirect()->route('home');
        }

        $student->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'father_name' => $request->father_name,
            'father_phone' => $request->father_phone,
            'father_email' => $request->father_email,
            'mother_name' => $request->mother_name,
            'mother_phone' => $request->mother_phone,
            'mother_email' => $request->mother_email,
            'nationality' => $request->nationality,
            'dob' => $request->dob,
            'born_at' => $request->born_at,
            'class_section_id' => $request->class_section_id,
            'admission_date' => $request->admission_date,
            'status' => $request->status,
            'repeater' => $request->repeater,
            'image' => $request->image,
        ]);

        $student->save();

        return redirect()->route('student.index');
    }

    public function delete(Students $student)
    {
        if (!Auth::user()->can('student-delete')) {
            return redirect()->route('home');
        }

        $student->delete();

        return redirect()->route('student.index');
    }
}