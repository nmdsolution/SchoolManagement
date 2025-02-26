<?php

namespace App\Livewire;

use Livewire\Component;

class SequentialExamCreate extends Component
{
    public $exam_terms = [];

    public $class_sections = null;

    public function render()
    {
        return view('livewire.sequential-exam-create');
    }
}
