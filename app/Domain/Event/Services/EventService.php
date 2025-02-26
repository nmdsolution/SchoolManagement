<?php

namespace App\Domain\Event\Services;

use App\Domain\Event\Exporter\EventPdfExporter;
use App\Domain\Event\Repositories\EventRepository;
use App\Models\Event;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class EventService
{
    public function __construct(
        private EventRepository $eventRepository,
        private EventPdfExporter $pdfExporter
        )
    {
    }

    public function createEvent(array $data): Event
    {
        return DB::transaction(function () use ($data) {
            return $this->eventRepository->createEvent($data);
        });
    }

    public function updateEvent(int $id, array $data): Event
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->eventRepository->updateEvent($id, $data);
        });
    }

    public function getEventsList(array $params): array|Response
    {
        $data = $this->eventRepository->getEventsList($params);

        if (!empty($params['print'])) {
            return $this->pdfExporter->export($data['rows']);
        }

        return $data;
    }
}