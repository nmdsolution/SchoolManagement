<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\Department;

class DepartmentCrud extends Component
{
    public $departments, $departmentId, $name, $responsibleId;
    public $users;
    public $isEditMode = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'responsibleId' => 'nullable|exists:users,id',
    ];

    public function mount()
    {
        $this->departments = Department::with('responsible')->get();
        $this->users = User::whereHas('teacher', function($q) {
            return $q->whereHas('center_teacher', function($q2) {
                return $q2->where('center_id', get_center_id());
            });
        })->get(); // Charger tous les utilisateurs
    }

    public function resetForm()
    {
        $this->name = '';
        $this->responsibleId = null;
        $this->isEditMode = false;
    }

    public function validateResponsible()
    {
        if ($this->responsibleId) {
            $query = Department::where('responsible_id', $this->responsibleId);
            if ($this->isEditMode) {
                $query->where('id', '!=', $this->departmentId);
            }

            if ($query->exists()) {
                $this->addError('responsibleId', 'Cet utilisateur est déjà responsable d’un autre département.');
            }
        }
    }

    public function store()
    {
        $this->validate();
        $this->validateResponsible();

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        Department::create([
            'name' => $this->name,
            'responsible_id' => $this->responsibleId,
            'session_year_id' => getSessionYearData()->id,
            'medium_id' => getCurrentMedium()->id,
            'center_id' => get_center_id(),
        ]);
        $this->refreshData();
        $this->resetForm();
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $this->departmentId = $department->id;
        $this->name = $department->name;
        $this->responsibleId = $department->responsible_id;
        $this->isEditMode = true;
    }

    public function update()
    {
        $department = Department::findOrFail($this->departmentId);
        $this->validate([
            'name' => 'required|string|max:255',
            'responsible_id' => 'nullable|exists:users,id|unique:departments,responsible_id,' . $department->id,
        ]);
        $department->update([
            'name' => $this->name,
            'responsible_id' => $this->responsibleId,
        ]);
        $this->refreshData();
        $this->resetForm();
    }

    public function delete($id)
    {
        Department::findOrFail($id)->delete();
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->departments = Department::with('responsible')->get();
    }

    public function render()
    {
        return view('livewire.department-crud');
    }
}
