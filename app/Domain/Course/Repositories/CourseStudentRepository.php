<?php
namespace App\Domain\Course\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CourseStudentRepository extends BaseRepository
{
    public function getReportData(array $params): array
    {
        $query = $this->buildReportQuery($params);
        
        $total = $query->count();
        
        $results = $query->orderBy('id', 'DESC')
            ->skip($params['offset'])
            ->take($params['limit'])
            ->get();

        return [
            'total' => $total,
            'rows' => $this->formatReportData($results)
        ];
    }

    private function buildReportQuery(array $params): Builder
    {
        $query = $this->model->whereNot('id', 0);

        $this->applySearchFilter($query, $params['search'] ?? null);
        $this->applyDateFilter($query, $params);

        return $query;
    }

    private function applySearchFilter(Builder $query, ?string $search): void
    {
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhereHas('course', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('student.user', function ($q) use ($search) {
                    $q->where(DB::raw('CONCAT_WS(" ", first_name, last_name)'), 'like', "%$search%");
                })
                ->orWhereHas('student.center', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                });
            });
        }
    }

    private function applyDateFilter(Builder $query, array $params): void
    {
        if (isset($params['filter'])) {
            $query->where(function ($q) use ($params) {
                match ($params['filter']) {
                    1 => $q->whereDate('created_at', Carbon::today()),
                    2 => $q->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
                    3 => $q->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]),
                    4 => $q->whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]),
                    default => null
                };
            });
        }

        if (!empty($params['start_date'])) {
            $query->where('created_at', '>=', date('Y-m-d', strtotime($params['start_date'])));
        }

        if (!empty($params['end_date'])) {
            $query->where('created_at', '<=', date('Y-m-d', strtotime($params['end_date'])));
        }
    }

    private function formatReportData(Collection $results): array
    {
        $dateFormat = getSettings('date_formate')['date_formate'] ?? 'd-m-Y';
        $rows = [];
        $no = 1;

        foreach ($results as $row) {
            $rows[] = [
                'id' => $row->id,
                'no' => $no++,
                'course_name' => $row->course->name,
                'price' => $row->price,
                'date' => date($dateFormat, strtotime($row->created_at)),
                'student_name' => $row->student->user->full_name,
                'center_name' => $row->student->center->name
            ];
        }

        return $rows;
    }
}