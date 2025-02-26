<?php


namespace App\Domain\Holiday\Repositories;

use App\Models\Holiday;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class HolidayRepository extends BaseRepository
{
    public function __construct(Holiday $holiday)
    {
        parent::__construct($holiday);
    }

    public function createHoliday(array $data): Holiday
    {
        return $this->create([
            'date' => $data['date'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'center_id' => get_center_id()
        ]);
    }

    public function updateHoliday(Holiday $holiday, array $data): Holiday
    {
        $this->update([
            'date' => $data['date'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null
        ], $holiday->id);

        return $holiday->fresh();
    }

    public function getHolidaysList(array $params): array
    {
        $query = $this->buildListQuery($params);
        
        $total = $query->count();

        $holidays = $query->orderBy($params['sort'], $params['order'])
            ->skip($params['offset'])
            ->take($params['limit'])
            ->get();

        return [
            'total' => $total,
            'rows' => $this->formatHolidaysData($holidays)
        ];
    }

    private function buildListQuery(array $params): Builder
    {
        $query = $this->model->owner()->where('id', '!=', 0);

        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('id', 'LIKE', "%{$params['search']}%")
                    ->orWhere('title', 'LIKE', "%{$params['search']}%")
                    ->orWhere('description', 'LIKE', "%{$params['search']}%")
                    ->orWhere('date', 'LIKE', "%{$params['search']}%");
            })->owner();
        }

        return $query;
    }

    private function formatHolidaysData(Collection $holidays): array
    {
        $dateFormat = getSettings('date_formate')['date_formate'];
        $rows = [];
        $no = 1;

        foreach ($holidays as $holiday) {
            $rows[] = [
                'id' => $holiday->id,
                'no' => $no++,
                'date' => date($dateFormat, strtotime($holiday->date)),
                'title' => $holiday->title,
                'description' => $holiday->description,
                'operate' => $this->generateOperateButtons($holiday)
            ];
        }

        return $rows;
    }

    private function generateOperateButtons(Holiday $holiday): string
    {
        $editButton = sprintf(
            '<a class="btn btn-xs btn-gradient-primary btn-rounded btn-icon editdata" 
                data-id="%d" title="Edit" data-toggle="modal" data-target="#editModal">
                <i class="fa fa-edit"></i>
            </a>',
            $holiday->id
        );

        $deleteButton = sprintf(
            '<a class="btn btn-xs btn-gradient-danger btn-rounded btn-icon deletedata" 
                data-id="%d" data-url="%s" title="Delete">
                <i class="fa fa-trash"></i>
            </a>',
            $holiday->id,
            url('holiday', $holiday->id)
        );

        return $editButton . '&nbsp;&nbsp;' . $deleteButton;
    }
}