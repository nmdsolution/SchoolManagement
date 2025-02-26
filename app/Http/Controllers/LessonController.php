<?php

namespace App\Http\Controllers;

use App\Domain\Lesson\Services\LessonService;
use App\Exceptions\LessonHasTopicsException;
use App\Http\Requests\Lesson\ShowLessonsRequest;
use App\Http\Requests\Lesson\StoreLessonRequest;
use App\Http\Requests\Lesson\UpdateLessonRequest;
use App\Models\ClassSection;
use App\Models\File;
use App\Models\Lesson;
use App\Models\Subject;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Throwable;

class LessonController extends Controller 
{
    public function __construct(private LessonService $lessonService)
    {
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if (!Auth::user()->can('lesson-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $class_section = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();
        $subjects = Subject::SubjectTeacher()->orderBy('id', 'ASC')->get();


        return response(view('lessons.index', compact('class_section', 'subjects')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLessonRequest $request)
    {
        try {
            $this->lessonService->createLesson($request->validated());

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
                'exception' => $e->getMessage() . $e->getFile() . $e->getLine()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\SubjectLesson $subjectLesson
     * @return \Illuminate\Http\Response
     */
    public function show(ShowLessonsRequest $request)
    {
        try {
            $data = $this->lessonService->getLessonsList(
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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SubjectLesson $subjectLesson
     * @return \Illuminate\Http\Response
     */

     public function update(UpdateLessonRequest $request, int $id)
     {
         try {
             $this->lessonService->updateLesson($id, $request->validated());
 
             return response()->json([
                 'error' => false,
                 'message' => trans('data_store_successfully')
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
                 'exception' => $e->getMessage() . $e->getFile() . $e->getLine()
             ], 500);
         }
     }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\SubjectLesson $subjectLesson
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        try {
            $this->lessonService->deleteLesson($id);

            return response()->json([
                'error' => false,
                'message' => trans('data_delete_successfully')
            ]);

        } catch (LessonHasTopicsException $e) {
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
    }


    public function search(Request $request) {
        $lesson = new Lesson;
        if (isset($request->subject_id)) {
            $lesson = $lesson->where('subject_id', $request->subject_id);
        }

        if (isset($request->class_section_id)) {
            $lesson = $lesson->where('class_section_id', $request->class_section_id);
        }
        $lesson = $lesson->get();
        $response = array(
            'error'   => false,
            'data'    => $lesson,
            'message' => 'Lesson fetched successfully'
        );
        return response()->json($response);
    }

    public function deleteFile($id) {
        try {
            $file = File::findOrFail($id);
            if (Storage::disk('public')->exists($file->file_url)) {
                Storage::disk('public')->delete($file->file_url);
            }
            $file->delete();
            $response = array(
                'error'   => false,
                'message' => trans('data_delete_successfully')
            );
        } catch (\Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }
}
