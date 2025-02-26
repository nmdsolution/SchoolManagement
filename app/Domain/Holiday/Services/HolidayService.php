<?php


namespace App\Domain\Holiday\Services;

use App\Domain\Holiday\Exporter\HolidayPdfExporter;
use App\Domain\Holiday\Repositories\HolidayRepository;
use App\Models\Holiday;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class HolidayService
{
    public function __construct(
        private HolidayRepository $holidayRepository,
        private HolidayPdfExporter $pdfExporter
        )
    {
        
    }

    public function createHoliday(array $data): Holiday
    {
        $data['date'] = date('Y-m-d', strtotime($data['date']));

        return DB::transaction(function () use ($data) {
            return $this->holidayRepository->createHoliday($data);
        });
    }

    public function updateHoliday(int $holidayId, array $data): Holiday
    {
        return DB::transaction(function () use ($holidayId, $data) {
            $holiday = $this->holidayRepository->getByIdOrFail($holidayId);
            return $this->holidayRepository->updateHoliday($holiday, $data);
        });
    }

    public function getHolidaysData(array $params): array|Response
    {
        $data = $this->holidayRepository->getHolidaysList($params);

        if (!empty($params['print'])) {
            return $this->pdfExporter->export($data['rows']);
        }

        return $data;
    }

}