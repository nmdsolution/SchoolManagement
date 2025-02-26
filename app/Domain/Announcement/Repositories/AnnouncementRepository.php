<?php

namespace App\Domain\Announcement\Repositories;

use App\Models\Announcement;
use App\Models\File;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AnnouncementRepository extends BaseRepository
{

    public function __construct(Announcement $announcement)
    {
        parent::__construct($announcement);
    }

    public function create( $data): Announcement
    {
        $announcement = new Announcement();
        $announcement->fill($data);
        
        if (isset($data['table'])) {
            $announcement->table()->associate($data['table']);
        }
        
        $announcement->save();
        return $announcement;
    }

    public function attachFiles(Announcement $announcement, array $files): void
    {
        
        foreach ($files as $uploadedFile) {
            $file = new File();
            $file->file_name = $uploadedFile->getClientOriginalName();
            $file->type = 1;
            $file->file_url = $uploadedFile->store('announcement', 'public');
            $file->modal()->associate($announcement);
            $file->save();
        }
    }

    public function getPaginatedAnnouncements(array $filters, array $pagination): Collection
    {
        return $this->buildAnnouncementQuery($filters)
            ->orderBy($pagination['sort'], $pagination['order'])
            ->skip($pagination['offset'])
            ->take($pagination['limit'])
            ->get();
    }

    public function getTotalCount(array $filters): int
    {
        return $this->buildAnnouncementQuery($filters)->count();
    }

    private function buildAnnouncementQuery(array $filters): Builder
    {
        $query = Announcement::with('table', 'file')->Owner();

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('id', 'LIKE', "%{$filters['search']}%")
                    ->orWhere('title', 'LIKE', "%{$filters['search']}%")
                    ->orWhere('description', 'LIKE', "%{$filters['search']}%");
            });

            if ($filters['isTeacher']) {
                $query->where('center_id', session()->get('center_id'));
            } else {
                $query->where('center_id', auth()->user()->center->id);
            }
        }

        return $query;
    }
}

