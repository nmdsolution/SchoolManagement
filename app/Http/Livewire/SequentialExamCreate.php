<?php

namespace App\Http\Livewire;

use App\Http\Controllers\ExamController;
use App\Models\ExamClassSection;
use App\Models\ExamTimetable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\ExamTerm;
use App\Models\ExamSequence;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\Exam;

class SequentialExamCreate extends Component
{
    // the select and other variables data
    public $exam_term_id;
    public $exam_sequence_id;
    public $class_section_id;
    public $timetable_subject_id;
    public $description;

    public $class_sections;
    public $exam_terms;


    protected $rules = [
        'exam_term_id' => 'required|exists:exam_terms,id',
        'exam_sequence_id' => 'required|exists:exam_sequences,id',
        'class_section_id' => 'required|exists:class_sections,id',
        'description' => 'nullable|string',
    ];

    public function mount() {
        $this->class_sections = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();

        $this->exam_terms = ExamTerm::owner()->currentSessionYear()->currentMedium()->get();
    }

    public function submit(): void
    {
        $validated = false;

        if ($this->class_section_id && $this->exam_term_id && $this->exam_sequence_id && $this->timetable_subject_id )  {
            $validated = true;
        }

        // validation and checking
        if ($validated) {
            // making sure that the record does not exist.
            try {
                $subject = Subject::find($this->timetable_subject_id);

                $session_year_id = getSettings('session_year',Auth::user()->center->id)['session_year'];

                if ($session_year_id) {

                    $exam = new Exam();
                    $exam->name = $subject->name;
                    $exam->type = 1; // 1 means it is a sequential exam.
                    $exam->description = $this->description;
                    $exam->session_year_id = $session_year_id;
                    $exam->exam_term_id = $this->exam_term_id;
                    $exam->exam_sequence_id = $this->exam_sequence_id;
                    $exam->teacher_status = 1;
                    $exam->student_status = 1;
                    $exam->center_id = Auth::user()->center->id;
                    $exam->save();

                    ExamClassSection::query()->insert([
                        'exam_id'          => $exam->id,
                        'class_section_id' => $this->class_section_id,
                    ]);

                    ExamTimetable::query()->insert([
                        'exam_id'          => $exam->id,
                        'class_section_id' => $this->class_section_id,
                        'subject_id'       => $this->timetable_subject_id,
                        'total_marks'      => 20,
                        'passing_marks'    => 10,
                        'session_year_id'  => $session_year_id
                    ]);

                    DB::commit();

                    $this->dispatchBrowserEvent('success-message', ['message' => trans('exam_created')]);
                }
            } catch (\Throwable $throwable) {
                DB::rollBack();
                $this->dispatchBrowserEvent('success-message', ['message' => trans("error_occured")]);
            }
        } else {
            $this->dispatchBrowserEvent('error-message', ['message' => trans('fill_all_form')]);
        }
    }

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {

        if ($this->class_section_id) {
            $subjects =  ClassSection::query()->find($this->class_section_id)->class->allSubjects->pluck('subject');
        } else {
            $subjects = Subject::owner()->activeMediumOnly()->get();
        }

        $presentSubjectIds = ExamClassSection::owner()->with([
            'class_section.class.stream',
            'class_section.section',
            'exam.session_year',
            'exam.term',
            'exam.sequence',
            'class_timetable.subject'
        ])->where("class_section_id", $this->class_section_id)
            ->whereHas('exam', function ($q) {
                $q->where('exam_term_id', $this->exam_term_id);
                $q->where('exam_sequence_id', $this->exam_sequence_id);
                $q->currentSessionYear();
            })->get()->pluck('class_timetable.0.subject.id')->toArray();


        // filtering the subjects whose exam has already been created.

        $subjects = $subjects->reject(function ($subject, $key) use ($presentSubjectIds) {
            return in_array($subject->id, $presentSubjectIds);
        });

        return view('livewire.sequential-exam-create', [
            'sequences' => ExamSequence::owner()->where('exam_term_id', $this->exam_term_id)->where('status', 1)->get(),
            'timetable_subjects' => $subjects
        ]);
    }
}
