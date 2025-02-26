<?php

namespace App\Http\Controllers\Competency;

use App\Domain\Student\Repositories\StudentsRepository;
use App\Http\Controllers\Controller;
use App\Http\Forms\ClassCompetencyForm;
use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\Competency\Competency;
use App\Models\Competency\CompetencyMark;
use App\Repositories\ClassSchoolRepository;
use App\Repositories\Competency\ClassCompetencyRepository;
use App\Repositories\Competency\CompetencyDomainRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class ClassCompetencyController extends Controller
{

    use FormBuilderTrait;

    public function __construct(
        protected ClassCompetencyRepository $classCompetencyRepository,
        protected ClassSchoolRepository $classSchoolRepository,
        protected CompetencyDomainRepository $competencyDomainRepository
    ) {}

    public function index(): Factory|View|Application
    {
        if (!Auth::user()->can('class-competency-list')) {
            return redirect()->route('home')->with('error', trans('no_permission_message'));
        }

        $data = [
            'classes' => $this->classSchoolRepository->list(),
            'competencies' => Competency::whereHas('competency_domain', function ($q) {
                $q->where('center_id', get_center_id())->activeMediumOnly();
            })->get(),
        ];

        return view('competency.class_competency.index', $data);
    }

    public function create(): View
    {
        if (!Auth::user()->can('class-competency-create')) {
            return redirect()->route('home')->with('error', trans('no_permission_message'));
        }

        $form = $this->form(ClassCompetencyForm::class, [
            'method' => 'POST',
            'url' => route('class-competency.store'),
        ]);


        return view('competency.class_competency.create', compact('form', 'competencies'));
    }

    public function edit(ClassSchool $class): View
    {
        if (!Auth::user()->can('class-competency-edit')) {
            return redirect()->route('home')->with('error', trans('no_permission_message'));
        }

        $class->load('competencies')->whereHas('competency_domain', function ($q) {
            $q->where('center_id', get_center_id())->activeMediumOnly();
        })->get();

        $form = $this->form(ClassCompetencyForm::class, [
            'method' => 'PUT',
            'url' => route('class-competency.update', ['class' => $class->id]),
            'model' => $class
        ]);

        $competency_domains = $this->competencyDomainRepository->list();
        return view('competency.class_competency.edit', compact('form', 'class', 'competency_domains'));
    }

    public function update(Request $request, ClassSchool $class): JsonResponse
    {
        if (!Auth::user()->can('class-competency-edit')) {
            return redirect()->route('home')->with('error', trans('no_permission_message'));
        }

        try {
            DB::beginTransaction();

            // Synchroniser les compétences avec la classe
            $class->competencies()->sync($request->competency_ids);

            DB::commit();

            return response()->json([
                'error' => false,
                'message' => trans('data_update_successfully')
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => true,
                'message' => $th->getMessage()
            ], 422);
        }
    }

    public function store(Request $request): JsonResponse
    {
        if (!Auth::user()->can('class-competency-create')) {
            return redirect()->route('home')->with('error', trans('no_permission_message'));
        }

        try {
            DB::beginTransaction();

            $class = ClassSchool::findOrFail($request->class_id);
            $class->competencies()->sync($request->competency_ids);

            DB::commit();

            return response()->json([
                'error' => false,
                'message' => trans('data_store_successfully')
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => true,
                'message' => $th->getMessage()
            ], 422);
        }
    }

    public function destroy(ClassSchool $class, Request $request, StudentsRepository $studentsRepository): JsonResponse
    {
        if (!Auth::user()->can('class-competency-delete')) {
            return redirect()->route('home')->with('error', trans('no_permission_message'));
        }

        try {

            $class_section = ClassSection::with('class', 'teacher')
                ->where('class', $class->id)->first();

            $students = $studentsRepository->getStudentListBuilder($class_section->id)->get();

            $marks = CompetencyMark::where('competency_id', $request->competency_id)
                ->where('session_year_id', getSessionYearData()->id)
                ->whereIn('student_id', $students->pluck('id'))
                ->get();
                
            if ($marks->count() > 0) {
                return response()->json([
                    'error' => true,
                    'message' => __('error_delete_class_competency_with_marks', [
                        'competency' => $request->competency->name,
                        'class' => $class->name
                    ])
                ]);
            }

            // Détacher la compétence spécifique de la classe
            $class->competencies()->detach($request->competency_id);

            return response()->json([
                'success' => true,
                'message' => __('Competency removed from class successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
