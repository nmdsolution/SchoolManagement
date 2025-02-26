<?php

namespace App\Http\Controllers\Competency;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Forms\CompetencyDomainForm;
use App\Models\Competency\CompetencyDomain;
use App\Repositories\ClassSchoolRepository;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Repositories\Competency\CompetencyDomainRepository;

class CompetencyDomainController extends Controller
{
    use FormBuilderTrait;

    public function __construct(
        protected CompetencyDomainRepository $repository,
        protected ClassSchoolRepository $classSchoolRepository
        )
    {

    }
    public function index()
    {
        if (!Auth::user()->can('competency-domain-list')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }

        $form = $this->form(CompetencyDomainForm::class, [
            'method' => 'POST',
            'url' => route('competency-domain.store')
        ]);

        return view('competency.competencyDomain.index', [
            'competencyDomains' => $this->repository->list(),
            'form' => $form,
            'classes' => $this->classSchoolRepository->list()
        ]);
    }

    public function store() 
    {
        if (!Auth::user()->can('competency-domain-create')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }

        $form = $this->form(CompetencyDomainForm::class);

        $form->redirectIfNotValid();

        $formValues = $form->getFieldValues();
        $formValues['center_id'] = Auth::user()->center->id;
        $formValues['session_year_id'] = getSessionYearData()->id;

        $this->repository->create($formValues);
        
        return redirect()->route('competency-domain.index');
    }

    public function edit(CompetencyDomain $competencyDomain)
    {
        if (!Auth::user()->can('competency-domain-edit')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }

        $edit = Route::currentRouteAction();
        $form = $this->form(CompetencyDomainForm::class, [
            'method' => 'PUT',
            'url' => route('competency-domain.update', $competencyDomain->id),
            'model' => $competencyDomain
        ]);

        return view('competency.competencyDomain.index', [
            'competencyDomains' => $this->repository->paginate(),
            'form' => $form,
            'edit' => $edit,
            'classes' => $this->classSchoolRepository->list()
        ]);
    }

    public function update(Request $request, CompetencyDomain $competencyDomain)
    {
        // dd($request->all(), $competencyDomain->id);

        $this->repository->update($request->only('name', 'number', 'total_marks', 'medium_id'), $competencyDomain->id);

        return redirect()->route('competency-domain.index');
    }

    public function destroy(CompetencyDomain $competencyDomain) {
        if (!Auth::user()->can('competency-domain-delete')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }


        $competencyDomain = CompetencyDomain::with(['competencies' => function($q) use($competencyDomain) {
            $q->where('competency_domain_id', $competencyDomain->id);
        }])->activeMediumOnly()->owner()->find($competencyDomain->id);

        if ($competencyDomain->competencies->count() > 0) {

            $competenciesListString = "";
                foreach ($competencyDomain->competencies as $competency) {
                    $competenciesListString .= "\n * " . $competency->name . " ";
                }


            return response()->json(['error' => true, 'message' => __('alert_delete_competency_domain_with_competencies', ['competenciesList' => $competenciesListString])]);
        }
        $this->repository->delete($competencyDomain->id);

        return response()->json(['error' => false, 'message' => 'Domain deleted successfully']);
    }
}