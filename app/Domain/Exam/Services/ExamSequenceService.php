<?php

namespace App\Domain\Exam\Services;

use App\Domain\Exam\Repositories\ExamSequenceRepository;
use App\Domain\Exam\Services\ExamService;
use App\Models\Exam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExamSequenceService
{
    protected ExamSequenceRepository $examSequenceRepository;
    protected ExamService $examService;

    public function __construct(ExamSequenceRepository $examSequenceRepository, ExamService $examService)
    {
        $this->examSequenceRepository = $examSequenceRepository;
        $this->examService = $examService;
    }

    public function create(array $data)
    {
        $data['center_id'] = Auth::user()->center->id;
        $data['start_date'] = date("Y-m-d", strtotime($data['start_date']));
        $data['end_date'] = date("Y-m-d", strtotime($data['end_date']));
        return $this->examSequenceRepository->create($data);
    }

    public function getSequences(array $examTermIds, int $offset, int $limit, string $sort, string $order, ?string $search = null): array
    {
        $total = $this->examSequenceRepository->countSequences($examTermIds, $search);
        $sequences = $this->examSequenceRepository->getSequences($examTermIds, $offset, $limit, $sort, $order, $search);

        $rows = [];
        $no = 1;
        foreach ($sequences as $sequence) {
            $rows[] = [
                'id' => $sequence->id,
                'no' => $no++,
                'name' => $sequence->name,
                'exam_term_id' => $sequence->exam_term_id,
                'term' => $sequence->term->name,
                'start_date' => $sequence->start_date,
                'end_date' => $sequence->end_date,
                'status' => $sequence->status,
                'auto_sequence_exam_class_section_id' => $sequence->auto_sequence_exam->pluck('class_section_id'),
                'auto_sequence_exam_class_section' => $sequence->auto_sequence_exam_class_section,
                'created_at' => $sequence->created_at,
                'updated_at' => $sequence->updated_at,
            ];
        }

        return [
            'total' => $total,
            'rows' => $rows,
        ];
    }

    public function update(int $id, array $data): array
    {
        try {
            DB::beginTransaction();
            $data['start_date'] = date("Y-m-d", strtotime($data['start_date']));
            $data['end_date'] = date("Y-m-d", strtotime($data['end_date']));
            $sequence = $this->examSequenceRepository->findSequence($id);
            $newClassSections = array_diff($data['class_section_id'], $sequence->auto_sequence_exam->pluck('class_section_id')->toArray());
            if ($data['status']) {
                $examUpdate = ['teacher_status' => 1];
            } else {
                $examUpdate = [
                    'teacher_status' => 0,
                    'student_status' => 0
                ];
            }
            if (count($newClassSections) > 0) {
                $this->examService->createDummyExams([
                    [
                        'term_id'     => $sequence->exam_term_id,
                        'sequence_id' => $sequence->id,
                    ]
                ], $newClassSections);
            }

            Exam::owner()->whereHas('sequence', static function ($q) use ($id) {
                $q->where('id', $id);
            })->update($examUpdate);

            $this->examSequenceRepository->updateSequence($id, $data);
            DB::commit();
            return [
                'error'   => false,
                'message' => trans('data_update_successfully')
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            return [
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e->getMessage()
            ];
        }
    }
}
