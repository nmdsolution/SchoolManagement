<?php

namespace App\Domain\SessionYear\Repositories;

use App\Domain\Fee\Repositories\InstallmentFeeRepository;
use App\Models\SessionYear;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SessionYearRepository extends BaseRepository
{
    public function __construct(SessionYear $model, private InstallmentFeeRepository $installmentFeeRepository)
    {
        parent::__construct($model);
    }

    public function createSessionYear(array $data): SessionYear
    {
        $sessionYear = $this->create([
            'name' => $data['name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'center_id' => get_center_id(),
            'include_fee_installments' => $data['fees_installment'],
            'fee_due_date' => $data['fees_due_date'],
            'fee_due_charges' => $data['fees_due_charges']
        ]);

        if ($data['fees_installment'] && !empty($data['installment_data'])) {
            $this->createInstallments($sessionYear, $data['installment_data']);
        }

        return $sessionYear;
    }

    private function createInstallments(SessionYear $sessionYear, array $installments): void
    {
        $installmentData = array_map(function ($installment) use ($sessionYear) {
            return [
                'name' => $installment['name'],
                'due_date' => date('Y-m-d', strtotime($installment['due_date'])),
                'due_charges' => $installment['due_charges'],
                'session_year_id' => $sessionYear->id,
                'center_id' => auth()->user()->center->id
            ];
        }, $installments);

        $this->installmentFeeRepository->insertMany($installmentData);
    }

    public function updateSessionYear(int $id, array $data): SessionYear
    {
        $sessionYear = $this->getByIdOrFail($id);

        $this->update([
            'name' => $data['name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'fee_due_date' => $data['fees_due_date'],
            'fee_due_charges' => $data['fees_due_charges'],
            'include_fee_installments' => $data['fees_installment']
        ], $id);

        if ($data['fees_installment'] && !empty($data['installment_data'])) {
            $this->handleInstallments($sessionYear, $data['installment_data']);
        } else {
            $this->deleteInstallments($sessionYear);
        }

        return $sessionYear->fresh(['fee_installments']);
    }

    private function handleInstallments(SessionYear $sessionYear, array $installments): void
    {
        foreach ($installments as $data) {
            if (!empty($data['id'])) {
                $this->installmentFeeRepository->updateOrRestore($data);
            } else {
                $this->installmentFeeRepository->create([
                    'name' => $data['name'],
                    'due_date' => date('Y-m-d', strtotime($data['due_date'])),
                    'due_charges' => $data['due_charges'],
                    'session_year_id' => $sessionYear->id,
                    'center_id' => auth()->user()->center->id
                ]);
            }
        }

        // Restaurer tous les installments existants
        $sessionYear->fee_installments->each(function ($installment) {
            $installment->restore();
        });
    }

    private function deleteInstallments(SessionYear $sessionYear): void
    {
        $sessionYear->fee_installments->each(function ($installment) {
            $installment->delete();
        });
    }

    public function getSessionsList(array $params): array
    {
        $query = $this->buildListQuery($params);
        
        $total = $query->count();

        $sessions = $query->orderBy($params['sort'], $params['order'])
            ->skip($params['offset'])
            ->take($params['limit'])
            ->get();

        return [
            'total' => $total,
            'rows' => $this->formatSessionsData($sessions)
        ];
    }

    private function buildListQuery(array $params): Builder
    {
        $query = $this->model
            ->where('center_id', auth()->user()->center->id)
            ->with('fee_installments');

        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('id', 'LIKE', "%{$params['search']}%")
                    ->orWhere('name', 'LIKE', "%{$params['search']}%")
                    ->orWhere('start_date', 'LIKE', "%{$params['search']}%")
                    ->orWhere('end_date', 'LIKE', "%{$params['search']}%")
                    ->orWhere('default', 'LIKE', "%{$params['search']}%");
            })->where('center_id', auth()->user()->center->id);
        }

        return $query;
    }

    private function formatSessionsData(Collection $sessions): array
    {
        $dateFormat = getSettings('date_formate')['date_formate'];
        $rows = [];
        $no = 1;

        foreach ($sessions as $session) {
            $rows[] = [
                'id' => $session->id,
                'no' => $no++,
                'name' => $session->name,
                'default' => $session->default,
                'start_date' => date($dateFormat, strtotime($session->start_date)),
                'end_date' => date($dateFormat, strtotime($session->end_date)),
                'fees_due_date' => date($dateFormat, strtotime($session->fee_due_date)),
                'fees_due_charges' => $session->fee_due_charges,
                'include_fee_installments' => $session->include_fee_installments,
                'fee_installments' => $session->fee_installments,
                'operate' => $this->generateOperateButtons($session)
            ];
        }

        return $rows;
    }

    private function generateOperateButtons(SessionYear $session): string
    {
        $editButton = sprintf(
            '<a class="btn btn-xs btn-gradient-primary btn-rounded btn-icon editdata" 
                data-id="%d" data-url="%s" title="Edit" data-toggle="modal" data-target="#editModal">
                <i class="fa fa-edit"></i>
            </a>',
            $session->id,
            url('session-years')
        );

        $deleteButton = sprintf(
            '<a class="btn btn-xs btn-gradient-danger btn-rounded btn-icon deletedata" 
                data-id="%d" data-url="%s" title="Delete">
                <i class="fa fa-trash"></i>
            </a>',
            $session->id,
            url('session-years', $session->id)
        );

        return $editButton . '&nbsp;&nbsp;' . $deleteButton;
    }
}