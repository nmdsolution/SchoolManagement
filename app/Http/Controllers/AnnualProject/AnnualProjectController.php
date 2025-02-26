<?php

namespace App\Http\Controllers\AnnualProject;

use App\Models\ClassSchool;
use App\Models\ClassSubject;
use App\Models\ExamSequence;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AnnualProject\AnnualProject;
use App\Models\AnnualProject\AnnualProjectType;
use App\Models\AnnualProject\ClassAnnualProjectType;

class AnnualProjectController extends Controller
{
    public function show(Request $request) {

        $class_subjects = collect([]);
        $annualProjetcTypes = AnnualProjectType::owner()
            ->currentSessionYear()
            ->currentMediumOnly()
            ->get();
        
        if ($request->ajax()) {
            
            if ($request->class_subject_id)
            {
                $class_subjects = ClassSubject::where('id', $request->class_subject_id)->get();

                $sequences = ExamSequence::whereHas('term', function($q) {
                    $q->where('medium_id', getCurrentMedium()->id)->where('center_id', get_center_id())->currentSessionYear();
                })
                ->get();

                

                $classAnnualProjectTypes = ClassAnnualProjectType::whereIn('exam_sequence_id', $sequences->pluck('id'))
                    ->whereHas('annualProject', function($q) use($request) {
                        $q->where('class_subject_id', $request->class_subject_id)->currentSessionYear()->where('center_id', get_center_id());
                    })
                    ->get();

                $projectData = [];
                foreach ($sequences as $sequence) {
                    $row = [
                        'sequence_id' => $sequence->id,
                        'sequence_name' => $sequence->name
                    ];
                    
                    foreach ($annualProjetcTypes as $type) {
                        $value = $classAnnualProjectTypes
                            ->where('exam_sequence_id', $sequence->id)
                            ->where('annual_project_type_id', $type->id)
                            ->first();
                            
                        $row['types'][$type->id] = $value ? $value->total : null;
                }

                    $projectData[] = $row;
                }

                return response()->json([
                    'class_subjects' => $class_subjects,
                    'sequences' => $sequences,
                    'annual_project_types' => $annualProjetcTypes,
                    'project_data' => $projectData
                ]);
            }

            if ($request->class_id)
            {
                $class_subjects = ClassSubject::where('class_id', $request->class_id)->with('subject')->get();
                return response()->json([
                    'class_subjects' => $class_subjects,
                ]);
            }
        }

        $classes = ClassSchool::whereCenterId(get_center_id())->whereHas('class_section', function($q) {
            $q->where('medium_id', getCurrentMedium()->id);
        })->get();

        return view('annual-project.show', compact('classes', 'class_subjects', 'annualProjetcTypes'));
    }

    public function storeType(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            AnnualProjectType::create([
                'name' => $request->name,
                'center_id' => get_center_id(),
                'session_year_id' => getSessionYearData()->id,
                'medium_id' => getCurrentMedium()->id
            ]);

            return response()->json([
                'error' => false,
                'message' => 'Successfully created'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function storeCLassProject(Request $request, ClassSubject $classSubject)
    {        
        try {

            AnnualProject::updateOrCreate([
                'class_subject_id' => $classSubject->id,
                'session_year_id' => getSessionYearData()->id,
                'center_id' => get_center_id()
            ], [
                'class_subject_id' => $classSubject->id,
                'session_year_id' => getSessionYearData()->id,
                'center_id' => get_center_id()
            ]);
            
            foreach ($request->total as $sequenceId => $sequence) {
                foreach ($sequence as $typeId => $total) {
                    if ($total !== null) {
                        ClassAnnualProjectType::updateOrCreate([
                            'annual_project_type_id' => $typeId,
                            'exam_sequence_id' => $sequenceId
                        ], [
                            'total' => $total,
                            'annual_project_id' => AnnualProject::where('class_subject_id', $classSubject->id)->owner()->currentSessionYear()->first()->id,
                            'annual_project_type_id' => $typeId,
                            'exam_sequence_id' => $sequenceId
                        ]);
                    }
                }
            }

            return response()->json([
                'error' => false,
                'message' => 'Successfully created',
                'class_subject_id' => $classSubject->id
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function destroyType(Request $request, AnnualProjectType $annualProjectType)
    {
        $classAnnualProjectTypes = ClassAnnualProjectType::where('annual_project_type_id', $annualProjectType->id)
            ->whereHas('annualProject', function($q) {
                $q->owner()->currentSessionYear();
            })
            ->get();
        if ($classAnnualProjectTypes->count() > 0) {

            $class_names = $classAnnualProjectTypes->pluck('class_subject.class.name')->toArray();

            return response()->json([
                'error' => true,
                'message' => __('error_delete_used_annual_project_type', ['names' => implode(', ', $class_names)]),
                'unable' => true
            ]);
        }
        $annualProjectType->delete();
        return response()->json([
            'error' => false,
            'message' => 'Successfully deleted'
        ]);
    }

    public function forceDestroyType(Request $request, AnnualProjectType $annualProjectType)
    {
        if (!$annualProjectType) {
            return response()->json([
                'error' => true,
                'message' => __('error_annual_project_type_not_found', ['id' => $request->id])
            ]);
        }

        foreach ($annualProjectType->classAnnualProjectTypes as $classAnnualProjectType) {
            $classAnnualProjectType->delete();
        }

        return response()->json([
            'error' => false,
            'message' => 'Successfully deleted'
        ]);
    }
}
