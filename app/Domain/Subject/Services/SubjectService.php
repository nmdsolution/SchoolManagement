<?php


namespace App\Domain\Subject\Services;

use App\Domain\Subject\Repositories\SubjectRepository;
use App\Models\Subject;
use App\Printing\AcademicPrints;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SubjectService
{
    public function __construct(private SubjectRepository $subjectRepository)
    {
        
    }

    public function createSubject(array $data): Subject
    {
        return DB::transaction(function () use ($data) {
            return $this->subjectRepository->createSubject($data);
        });
    }

    public function updateSubject(int $id, array $data): Subject
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->subjectRepository->updateSubject($id, $data);
        });
    }

    public function deleteSubject(int $subjectId): void
    {
        DB::transaction(function () use ($subjectId) {
            $this->subjectRepository->deleteSubject($subjectId);
        });
    }

    public function canDeleteSubject(int $subjectId): bool
    {
        return !$this->subjectRepository->hasAssociations($subjectId);
    }

    public function getSubjectsData(array $params): array|Response
    {
        $data = $this->subjectRepository->getSubjectsList($params);

        if (!empty($params['print'])) {
            return $this->generatePDF($data['rows']);
        }

        return $data;
    }

    private function generatePDF(array $rows): Response
    {
        $pdf = AcademicPrints::getInstance(get_center_id());
        $pdf->printSubjectList($rows);

        return response(
            $pdf->Output('', 'SUBJECT LIST.pdf'),
            200,
            ['Content-Type' => 'application/pdf']
        );
    }
}