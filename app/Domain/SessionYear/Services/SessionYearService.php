<?php

namespace App\Domain\SessionYear\Services;

use App\Domain\SessionYear\Exporter\SessionPdfExporter;
use App\Domain\SessionYear\Repositories\SessionYearRepository;
use App\Models\SessionYear;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SessionYearService
{
    public function __construct(
        private SessionYearRepository $sessionYearRepository,
        private SessionPdfExporter $pdfExporter
    ) {}

    public function createSessionYear(array $data): SessionYear
    {
        return DB::transaction(function () use ($data) {
            return $this->sessionYearRepository->createSessionYear($data);
        });
    }

    public function updateSessionYear(int $id, array $data): SessionYear
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->sessionYearRepository->updateSessionYear($id, $data);
        });
    }

    public function getSessionsList(array $params): array|Response
    {
        $data = $this->sessionYearRepository->getSessionsList($params);

        if (!empty($params['print'])) {
            return $this->pdfExporter->export($data['rows']);
        }

        return $data;
    }
}