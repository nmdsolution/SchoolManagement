<?php
namespace App\Repositories\Competency;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Competency\Competency;

class CompetencyRepository extends BaseRepository
{
    public function __construct(Competency $model)
    {
        parent::__construct($model);
    }

    public function checkDuplicateCompetency(array $data): ?string
    {
        if (empty($data['classes'])) {
            return null;
        }

        foreach ($data['classes'] as $classId) {
            $existingCompetency = $this->model->whereHas('classes', function ($query) use ($classId) {
                $query->where('class_id', $classId);
            })->where(function ($query) use ($data) {
                $query->where('name', $data['name'])
                      ->orWhere('code', $data['code']);
            })->first();

            if ($existingCompetency) {
                return "Une compétence avec le même nom ou code existe déjà pour la classe sélectionnée.";
            }
        }

        return null;
    }

    public function create($data): mixed
    {
        // Vérification des doublons
        if ($errorMessage = $this->checkDuplicateCompetency($data)) {
            throw new \Exception($errorMessage);
        }

        $competency = parent::create([
            'competency_domain_id' => $data['competency_domain_id'],
            'name' => $data['name'],
        ]);

        if (isset($data['classes'])) {
            $competency->classes()->sync($data['classes']);
        }

        return $competency;
    }

    public function getForCenter()
    {
        return $this->model
            ->with(['competencyDomain', 'classes'])
            ->whereHas('competencyDomain', function($q) {
                $q->where('center_id', get_center_id())
                    ->activeMediumOnly();
            })
            ->paginate();
    }

    public function getForCenterUser()
    {
        return $this->model->owner()->get();
    }

    public function getForEdit(int $id)
    {
        $qb = $this->model->with('classes')->whereHas('competency_domain', function ($query) {
            $query->where('center_id', Auth::user()->center_id)->activeMediumOnly();
        });

        return $qb->find($id);
    }

}