<?php

namespace App\Http\Controllers\Competency;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Competency\CompetencyObservation;

class ObservationController extends Controller
{
    public function index()
    {
        return view('competency.observation.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'exam_term_id' => 'required|exists:exam_terms,id',
            'observation' => 'required|string',
            'teacher_signature' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'director_signature' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'parent_signature' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Sauvegarder les fichiers et obtenir les chemins
        $teacherSignaturePath = $request->file('teacher_signature') 
            ? $request->file('teacher_signature')->store('signatures/teachers', 'public')
            : null;

        $directorSignaturePath = $request->file('director_signature') 
            ? $request->file('director_signature')->store('signatures/directors', 'public')
            : null;

        $parentSignaturePath = $request->file('parent_signature') 
            ? $request->file('parent_signature')->store('signatures/parents', 'public')
            : null;

        // Enregistrer l'observation dans la base de données
        CompetencyObservation::create([
            'student_id' => $request->student_id,
            'exam_term_id' => $request->exam_term_id,
            'observation' => $request->observation,
            'teacher_signature' => $teacherSignaturePath,
            'director_signature' => $directorSignaturePath,
            'parent_signature' => $parentSignaturePath,
        ]);

        return response()->json([
            'error' => false,
            'message' => 'Observation enregistrée avec succès'
        ]);
    }
}
