<?php

namespace App\Http\Controllers\Competency;

use App\Http\Controllers\Controller;
use App\Models\Competency\LearningUnit;
use App\Models\ExamTerm;
use App\Models\ClassSchool;
use Illuminate\Http\Request;

class LearningUnitController extends Controller
{
    public function index()
    {
        $learningUnits = LearningUnit::with(['class', 'exam_term'])->get();
        $classes = ClassSchool::owner()->get();
        $examTerms = ExamTerm::owner()->get();

        return view('competency.learning_unit.index', compact('learningUnits', 'classes', 'examTerms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'exam_term_id' => 'required|exists:exam_terms,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        LearningUnit::create($validated);

        return response()->json([
            'success' => true,
            'message' => __('Learning Unit created successfully')
        ]);
    }

    public function update(Request $request, LearningUnit $learningUnit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'exam_term_id' => 'required|exists:exam_terms,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        $learningUnit->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('Learning Unit updated successfully')
        ]);
    }

    public function destroy(LearningUnit $learningUnit)
    {
        $learningUnit->delete();

        return response()->json([
            'success' => true,
            'message' => __('Learning Unit deleted successfully')
        ]);
    }
}