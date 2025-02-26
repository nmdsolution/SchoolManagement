<?php

namespace App\Domain\Exam\Repositories;

use App\Models\ExamClassSection;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ExamClassSectionRepository extends BaseRepository
{
    public function __construct(ExamClassSection $examClassSection)
    {
        parent::__construct($examClassSection);
    }

    /**
     * Récupère les examens pour l'affichage avec pagination et filtres
     *
     * @param array $params Paramètres de filtrage et pagination
     * @return array [total, Collection]
     */
    public function getForShow(array $params): array
    {
        $offset = $params['offset'] ?? 0;
        $limit = $params['limit'] ?? 10;
        $sort = $params['sort'] ?? 'id';
        $order = $params['order'] ?? 'DESC';
        $search = $params['search'] ?? null;
        $classSectionId = $params['class_section_id'] ?? null;

        $query = $this->baseQuery();

        $this->applySearch($query, $search);
        $this->applyClassSectionFilter($query, $classSectionId);

        $total = $query->count();

        $results = $query->orderBy($sort, $order)
            ->skip($offset)
            ->take($limit)
            ->get();

        return [$total, $results];
    }

    /**
     * Construit la requête de base avec les relations
     */
    private function baseQuery(): Builder
    {
        return $this->model->owner()
            ->with([
                'class_section.class',
                'class_section.section',
                'exam.session_year',
                'exam.term',
                'exam.sequence',
                'class_timetable.subject'
            ])
            ->whereHas('class_section.class', function ($q) {
                $q->activeMediumOnly();
            })
            ->whereHas('exam', function ($q) {
                $q->where('type', 2);
            });
    }

    /**
     * Applique les filtres de recherche à la requête
     */
    private function applySearch(Builder $query, ?string $search): void
    {
        if (empty($search)) {
            return;
        }

        $query->where(function ($q) use ($search) {
            $q->where('id', 'LIKE', "%$search%")
                ->orWhereHas('exam', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                        ->orWhere('description', 'LIKE', "%$search%");
                })
                ->orWhereHas('exam.term', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('exam.sequence', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('exam.session_year', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('class_section.class', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('class_section.section', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                });
        });
    }

    /**
     * Applique le filtre de section de classe
     */
    private function applyClassSectionFilter(Builder $query, ?int $classSectionId): void
    {
        if (!empty($classSectionId)) {
            $query->where('class_section_id', $classSectionId);
        }
    }

    public function updatePublishStatus(ExamClassSection $examClassSection, bool $status): void
    {
        $examClassSection->publish = $status;
        $examClassSection->save();
    }
}