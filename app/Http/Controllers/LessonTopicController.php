<?php

namespace App\Http\Controllers;

use App\Domain\Lesson\Repositories\LessonTopicRepository;
use App\Domain\Lesson\Services\LessonTopicService;
use App\Http\Requests\Lesson\ShowLessonTopicsRequest;
use App\Http\Requests\Lesson\StoreLessonTopicRequest;
use App\Http\Requests\Lesson\UpdateLessonTopicRequest;
use App\Models\ClassSection;
use App\Models\Lesson;
use App\Models\LessonTopic;
use App\Models\Subject;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Throwable;

class LessonTopicController extends Controller
{
    public function __construct(
        private LessonTopicRepository $lessonTopicRepository,
        private LessonTopicService $lessonTopicService
        )
    {
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('topic-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $class_section = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();
        $subjects = Subject::SubjectTeacher()->orderBy('id', 'ASC')->get();
        $lessons = Lesson::whereHas('subject', function ($q) {
            $q->where('subject_id', session()->get('center_id'));
        })
        ->whereHas('class_section.class',function($q) {
            $q->activeMediumOnly();
        })
        ->get();

        return response(view('lessons.topic', compact('class_section', 'subjects', 'lessons')));
    }

    /**
     *
     * /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLessonTopicRequest $request)
    {
        try {
            $this->lessonTopicRepository->createTopic($request->validated());

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
                'exception' => $e
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\LessonTopic $lessonTopic
     * @return \Illuminate\Http\Response
     */
    public function show(ShowLessonTopicsRequest $request)
    {
        
       $request->mergeIfMissing([
           'offset' => 0,
           'limit' => 10,
           'sort' => 'id',
           'order' => 'DESC'
       ]);

       if ($request->has('sort')) {
           $request->merge([
               'sort' => strtolower($request->sort)
           ]);
       }

       if ($request->has('order')) {
           $request->merge([
               'order' => strtoupper($request->order)
           ]);
       }

        try {
            $data = $this->lessonTopicService->getTopicsList(
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
     * @param \App\Models\LessonTopic $lessonTopic
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLessonTopicRequest $request, LessonTopic $lessonTopic)
    {
        try {
            $this->lessonTopicService->updateTopic(
                $request->validated('edit_id'),
                $request->validated()
            );

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
                'exception' => $e
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\LessonTopic $lessonTopic
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        try {
            $this->lessonTopicService->deleteTopic($id);

            return response()->json([
                'error' => false,
                'message' => trans('data_delete_successfully')
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
                    : $e->getMessage()
            ], 500);
        }
    }
}
