<?php

namespace App\Http\Controllers;

use App\Domain\Subject\Services\SubjectService;
use App\Exceptions\SubjectHasAssociationsException;
use App\Http\Requests\Subject\ShowSubjectsRequest;
use App\Http\Requests\Subject\StoreSubjectRequest;
use App\Http\Requests\Subject\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class SubjectController extends Controller {
    protected ?string $folder = null;
    public function __construct(private SubjectService $subjectService) {
        $this->folder = "subjects";
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $subjects = Subject::owner()->orderBy('id', 'DESC')->get();
        return response(view('subject.index', compact('subjects')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(StoreSubjectRequest $request)
    {
        try {
            $this->subjectService->createSubject($request->validated());

            return response()->json([
                'error' => false,
                'message' => trans('data_store_successfully')
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => app()->environment('production') 
                    ? trans('error_occurred') 
                    : $e->getMessage(),
                'data' => $e
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateSubjectRequest $request, int $id)
    {
        try {
            $this->subjectService->updateSubject($id, $request->validated());

            return response()->json([
                'error' => false,
                'message' => trans('data_update_successfully')
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => trans('record_not_found')
            ], 404);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => app()->environment('production') 
                    ? trans('error_occurred') 
                    : $e->getMessage(),
                'data' => $e
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $this->subjectService->deleteSubject($id);

            return response()->json([
                'error' => false,
                'message' => trans('data_delete_successfully')
            ]);

        } catch (SubjectHasAssociationsException $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 422);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => trans('record_not_found')
            ], 404);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => app()->environment('production') 
                    ? trans('error_occurred') 
                    : $e->getMessage()
            ], 500);
        }
    }public function show(ShowSubjectsRequest $request)
    {
        try {
            $result = $this->subjectService->getSubjectsData(
                $request->validated()
            );

            if ($result instanceof Response) {
                return $result;
            }

            // dd($request->all());

            return response()->json($result);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => app()->environment('production') 
                    ? trans('error_occurred') 
                    : $e->getMessage()
            ], 500);
        }
    }
}
