<?php

namespace App\Http\Livewire;

use App\Models\Subject;
use App\Models\Teacher;
use Livewire\Component;

class AssignTeacherSubjects extends Component
{
    public $teachers;
    public $subjects;
    public $selectedTeacher;
    public $selectedSubjects = [];

    public function mount() {
        $this->teachers = Teacher::with('user')->owner()->where()->get();
        $this->subjects = Subject::owner()->activeMediumOnly()->get();
    }

    public function assignTeacherSubjects(): void {
       try {
           $teacher = Teacher::find($this->selectedTeacher);
           $teacher->subjects()->sync($this->selectedSubjects);

           // send the success message when the operation is successfull
       } catch (\Throwable $throwable) {

            // flash the error message.

       }
    }

    public function render()
    {
        return view('livewire.assign-teacher-subjects');
    }
}
