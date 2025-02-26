<?php

namespace App\Http\Controllers;

use App\Domain\Course\Services\CourseCategoryService;
use App\Http\Requests\Course\ShowCourseCategoriesRequest;
use App\Http\Requests\Course\StoreCourseCategoryRequest;
use App\Http\Requests\Course\UpdateCourseCategoryRequest;
use App\Models\CourseCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Throwable;


class CourseCategoryController extends Controller
{
    public function __construct(private CourseCategoryService $courseCategoryService)
    {
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('course-list')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        return view('course_category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCourseCategoryRequest $request)
    {
        try {
            $this->courseCategoryService->createCategory($request->validated());

            return response()->json([
                'error' => false,
                'message' => trans('data_store_successfully')
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => app()->environment('production') 
                    ? trans('error_occurred') 
                    : $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ShowCourseCategoriesRequest $request)
    {
        try {
            $data = $this->courseCategoryService->getCategoriesList(
                $request->validated()
            );

            return response()->json($data);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => app()->environment('production') 
                    ? trans('error_occurred') 
                    : $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('course-edit')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $category = CourseCategory::find($id);
        return view('course_category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCourseCategoryRequest $request, int $id)
    {
        try {
            $this->courseCategoryService->updateCategory($id, $request->validated());

            return redirect('course_category')
                ->with('success', 'Data Update Successfully');

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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        if (!Auth::user()->can('course-delete')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        try {
            $this->courseCategoryService->deleteCategory($id);

            return response()->json([
                'error' => false,
                'message' => trans('data_deleted_successfully')
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
                'data' => $e->getMessage()
            ], 500);
        }
    }
}
