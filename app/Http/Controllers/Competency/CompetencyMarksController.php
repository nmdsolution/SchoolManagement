<?php

namespace App\Http\Controllers\Competency;

use App\Domain\Student\Repositories\StudentsRepository;
use App\Http\Controllers\Controller;
use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\Competency\ClassCompetency;
use App\Models\Competency\Competency;
use App\Models\Competency\CompetencyMark;
use App\Models\ExamSequence;
use App\Models\ExamTerm;
use App\Models\Students;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class CompetencyMarksController extends Controller
{
    public function __construct(private StudentsRepository $studentsRepository) {}

    public function index(): View
    {
        // Récupérer les compétences de l'enseignant connecté
        $q = Competency::with('competency_domain')->whereHas('competency_domain', function ($q) {
            $q->where('center_id', get_center_id())->activeMediumOnly();
        });

        $q->whereHas('competency_domain', function ($query) {
            $query->owner()->where('session_year_id', getSessionYearData()->id)->activeMediumOnly();
        });

        if (Auth::user()->hasRole('Teacher')) {
            $q->whereHas('classes', function ($query) {
                $query->whereHas('class_section', function ($query) {
                    $query->where('class_teacher_id', Auth::user()->teacher->id);
                });
            });
        }

        $data = [];

        $data['competencies'] =  $q->get();

        // Récupérer les trimestres
        $data['terms'] = ExamTerm::owner()
            ->currentSessionYear()
            ->currentMedium()
            ->orderBy('id', 'ASC')
            ->get();

        $data['sequences'] = ExamSequence::owner()->whereIn('exam_term_id', $data['terms']->pluck('id'))->where('status', 1)->get();

        $classQb = ClassSchool::owner()->whereCenterId(get_center_id())->activeMediumOnly();

        if (Auth::user()->hasRole('Teacher')) {
            $classQb = $classQb->whereHas('class_section', function ($q) {
                $q->where('class_teacher_id', Auth::user()->teacher->id);
            });
        }

        $data['classes'] = $classQb->get();

        return view('competency.marks.index', $data);
    }

    public function getStudents(Request $request): JsonResponse
    {
        $competencyId = $request->competency_id;
        $termId = $request->term_id;
        $sequenceId = $request->sequence_id;
        $class_id = $request?->class_id;

        $termId = ExamSequence::with('term')->find($sequenceId)->term->id;


        // Récupérer la classe
        $class_section = ClassSection::with('class', 'teacher')
            ->whereHas('class', function ($q) use ($class_id, $competencyId) {
                $q->where('id', $class_id)->whereHas('competencies', function ($q) use ($competencyId) {
                    $q->where('competency_id', $competencyId);
                });
            })
            ->first();

        // dd($class_section);


        if (!$class_section) {
            return response()->json([
                'error' => true,
                'message' => 'No data found'
            ]);
        }

        // Récupérer les étudiants
        $students = $this->studentsRepository->getStudentListForCompetencyMarks($class_section->id)->get();

        // Récupérer la compétence avec ses types
        $classCompetency = ClassCompetency::with(['competencyTypes'])
            ->where('class_id', $class_id)
            ->where('competency_id', $competencyId)
            ->first();

        if (!$classCompetency) {
            return response()->json([
                'error' => true,
                'message' => 'Competency not found'
            ]);
        }

        // Récupérer les notes existantes avec les relations nécessaires
        $marks = CompetencyMark::with('competencyType')
            ->where('competency_id', $competencyId)
            ->where('exam_term_id', $termId)
            ->where('exam_sequence_id', $sequenceId)
            ->where('session_year_id', getSettings('session_year')['session_year'])
            ->whereIn('student_id', $students->pluck('id'))
            ->get();

        // Organiser les notes par étudiant et type de compétence
        $studentMarks = [];
        foreach ($marks as $mark) {
            if (!isset($studentMarks[$mark->student_id])) {
                $studentMarks[$mark->student_id] = [
                    'marks' => [],
                    'total' => 0
                ];
            }
            $studentMarks[$mark->student_id]['marks'][$mark->competency_type_id] = [
                'mark' => $mark->obtained_marks,
                'type_name' => $mark->competencyType->name,
                'max_marks' => $mark->competencyType->pivot->total_marks ?? 20
            ];
            $studentMarks[$mark->student_id]['total'] += $mark->obtained_marks;
        }

        // Mapper les données des étudiants
        $students = $students->map(function ($student) use ($studentMarks) {
            return [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'marks' => $studentMarks[$student->id]['marks'] ?? [],
                'total' => $studentMarks[$student->id]['total'] ?? 0
            ];
        })->sortBy('full_name')->values();

        return response()->json([
            'error' => false,
            'students' => $students,
            'competency' => [
                'id' => $classCompetency->competency->id,
                'name' => $classCompetency->competency->name,
                'domain' => $classCompetency->competency->competency_domain->name,
                'types' => $classCompetency->competencyTypes->map(function ($type) {
                    return [
                        'id' => $type->id,
                        'name' => $type->name,
                        'total_marks' => $type->pivot->total_marks ?? 20
                    ];
                })
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (!Auth::user()->can('exam-upload-marks')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }

        $term = ExamSequence::with('term')->find($request->sequence_id)->term;
        // dd($request->all());
        try {
            DB::beginTransaction();

            // Récupérer la compétence avec ses types et la relation avec la classe
            $classCompetency = ClassCompetency::with(['competencyTypes'])
                ->where('class_id', $request->class_id)
                ->where('competency_id', $request->competency_id)
                ->first();



            // Valider les notes pour chaque élève
            foreach ($request->marks as $studentId => $typeMarks) {
                $totalMarks = 0;

                foreach ($typeMarks as $typeId => $mark) {
                    $type = $classCompetency->competencyTypes->firstWhere('id', $typeId);
                    if (!$type) {
                        throw new \Exception(__('Invalid competency type'));
                    }

                    $maxMarks = $type->pivot->total_marks ?? 20;
                    if ($mark > $maxMarks) {
                        throw new \Exception(__('Mark exceeds maximum allowed for type :type', ['type' => $type->name]));
                    }

                    // Sauvegarder la note pour ce type de compétence
                    CompetencyMark::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'competency_id' => $request->competency_id,
                            'competency_type_id' => $typeId,
                            'exam_term_id' => $term->id,
                            'exam_sequence_id' => $request->sequence_id,
                            'session_year_id' => getSettings('session_year')['session_year']
                        ],
                        [
                            'obtained_marks' => $mark,
                            'passing_status' => ($mark >= ($maxMarks / 2))
                        ]
                    );

                    $totalMarks += $mark;
                }
            }

            DB::commit();

            return response()->json([
                'error' => false,
                'message' => __('Marks saved successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function uploadStudentMarks()
    {
        if (!Auth::user()->can('exam-upload-marks')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $students = Students::owner()->whereHas('studentSessions', function ($query) {
            $query->where('session_year_id', getSettings('session_year')['session_year']);
        })->get();

        $classesQb = ClassSchool::with('class_section')->where('center_id', get_center_id());

        if (Auth::user()->hasRole('Teacher')) {
            $classesQb->whereHas('class_section', function ($q) {
                $q->where('class_teacher_id', Auth::user()->teacher->id);
            });
        }

        $classes = $classesQb->get();

        $sequences = ExamSequence::owner()->where('status', 1)->get();


        return view('competency.marks.upload-student', compact('students', 'classes', 'sequences'));
    }

    public function studentsList(Request $request)
    {
        if (!Auth::user()->can('exam-upload-marks')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $class_id = $request->class_id;

        $class_section = ClassSection::with('class', 'teacher')
            ->whereHas('class', function ($q) use ($class_id) {
                $q->where('id', $class_id);
            })->first();

        $students = $this->studentsRepository->getStudentListBuilder($class_section->id)->get();

        // dd($students->toArray());

        return response()->json(compact('students'));
    }

    public function uploadStudentMarksStore(Request $request)
    {
        if (!Auth::user()->can('exam-upload-marks')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $students = Students::owner()->whereHas('studentSessions', function ($query) {
            $query->where('session_year_id', getSettings('session_year')['session_year']);
        })->get();

        $classes = ClassSchool::owner()->get();

        try {
            DB::beginTransaction();

            $classId = $request->class_id;
            $studentId = $request->student_id;

            $class = ClassSchool::find($classId);
            $student = Students::find($studentId);

            foreach ($request->marks as $competencyId => $typeMarks) {
                $competency = Competency::with('competency_types')->find($competencyId);

                foreach ($typeMarks as $typeId => $mark) {
                    $type = $competency->competency_types->firstWhere('id', $typeId);
                    if (!$type) {
                        throw new \Exception(__('Invalid competency type'));
                    }

                    CompetencyMark::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'competency_id' => $competencyId,
                            'competency_type_id' => $typeId,
                            'exam_term_id' => $request->exam_term_id,
                            'exam_sequence_id' => $request->exam_sequence_id,
                            'session_year_id' => getSettings('session_year')['session_year']
                        ],
                        [
                            'obtained_marks' => $mark,
                            'passing_status' => ($mark >= 25) // 25 sur 50
                        ]
                    );
                }
            }

            DB::commit();
            $response = array(
                'error'   => false,
                'message' => trans('data_store_successfully'),
            );
        } catch (Throwable $e) {
            DB::rollBack();
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e->getMessage()
            );
        }
        return response()->json($response);
    }

    public function edit(): View
    {
        // Récupérer les séquences actives
        $sequences = ExamSequence::owner()
            ->whereHas('term', function ($q) {
                $q->currentSessionYear()
                    ->currentMedium();
            })
            ->where('status', 1)
            ->get();

        // Récupérer les classes
        $classes = ClassSchool::owner()->get();

        return view('competency.marks.edit', compact('sequences', 'classes'));
    }

    public function updateStudentMarks(Request $request): JsonResponse
    {
        $student_id = $request->student_id;

        try {
            DB::beginTransaction();

            $student = Students::findOrFail($student_id);
            $sequence = ExamSequence::findOrFail($request->sequence_id);
            $termId = $sequence->term->id;

            foreach ($request->marks as $competencyId => $typeMarks) {
                $classCompetency = ClassCompetency::with('competencyTypes')
                    ->whereHas('competency', function ($q) use ($competencyId) {
                        $q->where('id', $competencyId);
                    })->first();

                foreach ($typeMarks as $typeId => $mark) {
                    $type = $classCompetency->competencyTypes->firstWhere('id', $typeId);
                    if (!$type) {
                        throw new \Exception(__('Invalid competency type'));
                    }

                    CompetencyMark::updateOrCreate(
                        [
                            'student_id' => $student_id,
                            'competency_id' => $competencyId,
                            'competency_type_id' => $typeId,
                            'exam_term_id' => $termId,
                            'exam_sequence_id' => $request->sequence_id,
                            'session_year_id' => getSettings('session_year')['session_year']
                        ],
                        [
                            'obtained_marks' => $mark,
                            'passing_status' => ($mark >= 25) // 25 sur 50
                        ]
                    );
                }
            }

            DB::commit();
            return response()->json(['error' => false, 'message' => trans('data_store_successfully')]);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => true, 'message' => trans('error_occurred'), 'data' => $e->getMessage()]);
        }
    }

    public function getClassCompetencies(Request $request)
    {
        if (!Auth::user()->can('exam-upload-marks')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }

        $sequence_id = $request->sequence_id;
        $class_id = $request->class_id;
        $student_id = $request->student_id;
        $termId = ExamSequence::with('term')->find($sequence_id)->term->id;

        $class_section = ClassSection::with('class', 'teacher')
            ->whereHas('class', function ($q) use ($class_id) {
                $q->where('id', $class_id)->whereHas('competencies');
            })->first();


        if (!$class_section) {
            return response()->json([
                'error' => true,
                'message' => 'No data found'
            ]);
        }

        // Récupérer les étudiants
        $student = $this->studentsRepository->getStudentListBuilder($class_section->id)->find($student_id);


        // Récupérer la compétence avec ses types
        $competencies = ClassCompetency::with(['competencyTypes'])
            ->whereHas('competency.competency_domain', function ($q) {
                $q->where('center_id', get_center_id())
                    ->where('session_year_id', getSessionYearData()->id)
                    ->activeMediumOnly();
            })
            ->where('class_id', $class_id)
            ->get();


        if (!$competencies) {
            return response()->json([
                'error' => true,
                'message' => 'Competency not found'
            ]);
        }

        // Récupérer les notes existantes avec les relations nécessaires
        $marks = CompetencyMark::with('competencyType')
            ->where('exam_term_id', $termId)
            ->where('exam_sequence_id', $sequence_id)
            ->where('session_year_id', getSettings('session_year')['session_year'])
            ->where('student_id', $student->id)
            ->get();

        // Organiser les notes par étudiant et type de compétence
        $studentMarks = [];
        $count = 0;
        foreach ($marks as $mark) {
            if (!isset($studentMarks[$mark->competency_id])) {
                $studentMarks[$mark->competency_id] = [
                    'marks' => [],
                    'total' => 0
                ];
            }

            $studentMarks[$mark->competency_id]['marks'][$mark->competency_type_id] = [
                'mark' => $mark->obtained_marks,
                'type_name' => $mark->competencyType->name,
                'max_marks' => $mark->competencyType->pivot->total_marks ?? 20
            ];
            $studentMarks[$mark->competency_id]['total'] += $mark->obtained_marks;
        }

        // dd($studentMarks);


        return response()->json([
            'error' => false,
            'student' => $student,
            'competencies' => $competencies,
            'marks' => $studentMarks
        ]);
    }



    public function reportCardList() {}
}
