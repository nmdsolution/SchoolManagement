<?php

namespace App\Http\Controllers\Competency;

use App\Domain\Student\Repositories\StudentsRepository;
use App\Http\Controllers\Controller;
use App\Http\Forms\CompetencyForm;
use App\Models\Competency\Competency;
use App\Models\Competency\CompetencyDomain;
use App\Repositories\Competency\CompetencyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class CompetencyController extends Controller
{
    use FormBuilderTrait;

    public function __construct(
        protected CompetencyRepository $competencyRepository,
        protected StudentsRepository $studentsRepository
    )
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View|JsonResponse
    {
        $competenciesQb = Competency::with('competency_domain')->whereHas('competency_domain', function ($query) {
            $query->where('center_id', get_center_id())->activeMediumOnly();
        });

        if ($request->ajax()) {
            if ($request->class_id) {
                $competencies = $competenciesQb->whereHas('classes', function($q) use($request) {
                    $q->where('class_id', $request->class_id);
                })->get();
            }
            return response()->json([
                'error' => false,
                'competencies' => $competencies,
            ]);
        }

        $competencies = $competenciesQb->get();

        $competency_domains = CompetencyDomain::where('center_id', get_center_id())->activeMediumOnly()->get();
        return view('competency.competency.index', compact('competencies', 'competency_domains'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        if (!Auth::user()->can('competency-create')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }
        $competencies = $this->competencyRepository->getForCenter();

        $form = $this->form(CompetencyForm::class,[
            'method' => 'POST',
            'url' => route('competency.store'),
        ]);


        return view('competency.competency.create', compact('form', 'competencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        if (!Auth::user()->can('competency-create')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|max:255',
            'competency_domain_id' => 'required|exists:competency_domains,id',
        ]);

        try {
            $this->competencyRepository->create($validated);
            return response()->json([
                'error' => false,
                'message' => trans('data_store_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Competency $competency): View
    {
        if (!Auth::user()->can('competency-list')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }

        return view('competency.competency.show', compact('competency'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Competency $competency): View
    {
        if (!Auth::user()->can('competency-edit')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }

        $form = $this->form(CompetencyForm::class, [
            'method' => 'POST',
            'url' => route('competency.update', $competency->id),
            'model' => $competency
        ]);
        return view('competency.competency.edit', compact('form', 'competency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Competency $competency): JsonResponse
    {
        if (!Auth::user()->can('competency-edit')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'competency_domain_id' => 'required|exists:competency_domains,id',
        ]);
        
        try {
            $competency->update($validated);

            return response()->json([
                'error' => false,
                'message' => trans('data_update_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Competency $competency): JsonResponse
    {
        if (!Auth::user()->can('competency-delete')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }

        try {
            $competency = Competency::with(['classes' => function($q) use($competency) {
                $q->where('competency_id', $competency->id);
            }])->whereHas('competency_domain', function($q) {
                $q->activeMediumOnly()->where('center_id', get_center_id());
            })->find($competency->id);

            if ($competency->classes->count() > 0) {
                $classesListString = "";
                foreach ($competency->classes as $classCompetency) {
                    $classesListString .= " * " . $classCompetency->pivot->class->name . " ";
                }

                return response()->json(['error' => true, 'message' => __('error_delete_competency_with_types', ['classList' => $classesListString])]);
            }
            
            $this->competencyRepository->delete($competency->id);

            return response()->json([
                'error' => false,
                'message' => trans('data_delete_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
