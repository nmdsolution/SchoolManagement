<?php

namespace App\Http\Livewire;

use App\Models\ClassSchool;
use App\Models\ReportCard;
use Livewire\Component;

class ReportCardAssignment extends Component
{
    public $classes;
    public $reportCardTypes;

    public array $selectedReportType = [];
    public array $selectedReportLayout = [];
    public array $selectedReportFooterTable = [];

    public function mount(): void {
        $this->classes = ClassSchool::owner()->activeMediumOnly()->get();
        $this->reportCardTypes = ReportCard::all();

        foreach ($this->classes as $class) {
            $this->selectedReportType[$class->id] = $class->report_card_id;
            $this->selectedReportLayout[$class->id] = $class->report_layout ?? 0;
            $this->selectedReportFooterTable[$class->id] = $class->report_footer_table ?? 'max';
        }
    }

    public function adjustFooterTable($classId): void {
        // If the selected layout is "New Layout" or "New Layout Without Header"
        if (in_array($this->selectedReportLayout[$classId], [1, 2])) {
            $this->selectedReportFooterTable[$classId] = 'min';
        }
    }

    public function assignTemplates(): void {
        foreach ($this->classes as $class) {
            $class->report_card_id = $this->selectedReportType[$class->id] ?? null;
            $class->report_layout = $this->selectedReportLayout[$class->id] ?? 0;
            $class->report_footer_table = $this->selectedReportFooterTable[$class->id] ?? 'max';
            $class->save();
        }

        $this->dispatchBrowserEvent('templates-assigned', ['message' => 'Report card templates assigned successfully!']);
    }

    public function render()
    {
        return view('livewire.report-card-assignment');
    }
}
