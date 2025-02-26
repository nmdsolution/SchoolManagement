<?php

namespace App\Domain\Event\Repositories;

use App\Models\Event;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EventRepository extends BaseRepository
{
    public function __construct(Event $model)
    {
        parent::__construct($model);
    }

    public function createEvent(array $data): Event
    {
        $sessionYear = getSettings('session_year');

        return $this->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'location' => $data['location'],
            'session_year_id' => $sessionYear['session_year'],
            'center_id' => get_center_id(),
            'medium_id' => getCurrentMedium()->id
        ]);
    }

    public function updateEvent(int $id, array $data): Event
    {
        $event = $this->getByIdOrFail($id);
        
        $this->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'location' => $data['location']
        ], $id);

        return $event->fresh();
    }

    public function getEventsList(array $params): array
    {
        $query = $this->buildListQuery($params);
        
        $total = $query->count();

        $events = $query->orderBy($params['sort'], $params['order'])
            ->skip($params['offset'])
            ->take($params['limit'])
            ->get();

        return [
            'total' => $total,
            'rows' => $this->formatEventsData($events)
        ];
    }

    private function buildListQuery(array $params): Builder
    {
        $query = $this->model->owner()
            ->activeMediumOnly()
            ->where('id', '!=', 0);

        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('id', 'LIKE', "%{$params['search']}%")
                    ->orWhere('name', 'LIKE', "%{$params['search']}%")
                    ->orWhere('location', 'LIKE', "%{$params['search']}%")
                    ->orWhere('end_date', 'LIKE', "%{$params['search']}%")
                    ->orWhere('start_date', 'LIKE', "%{$params['search']}%");
            })->owner();
        }

        return $query;
    }

    private function formatEventsData(Collection $events): array
    {
        $dateFormat = getSettings('date_formate')['date_formate'];
        $rows = [];
        $no = 1;

        foreach ($events as $event) {
            $rows[] = [
                'id' => $event->id,
                'no' => $no++,
                'name' => $event->name,
                'description' => $event->description,
                'start_date' => date($dateFormat, strtotime($event->start_date)),
                'end_date' => date($dateFormat, strtotime($event->end_date)),
                'location' => $event->location,
                'operate' => $this->generateOperateButtons($event)
            ];
        }

        return $rows;
    }

    private function generateOperateButtons(Event $event): string
    {
        $editButton = sprintf(
            '<a class="btn btn-xs btn-gradient-primary btn-rounded btn-icon editdata" 
                data-id="%d" title="Edit" data-toggle="modal" data-target="#editModal">
                <i class="fa fa-edit"></i>
            </a>',
            $event->id
        );

        $deleteButton = sprintf(
            '<a class="btn btn-xs btn-gradient-danger btn-rounded btn-icon deletedata" 
                data-id="%d" data-url="%s" title="Delete">
                <i class="fa fa-trash"></i>
            </a>',
            $event->id,
            url('event', $event->id)
        );

        return $editButton . '&nbsp;&nbsp;' . $deleteButton;
    }
}