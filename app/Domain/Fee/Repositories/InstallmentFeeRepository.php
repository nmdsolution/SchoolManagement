<?php

namespace App\Domain\Fee\Repositories;

use App\Models\InstallmentFee;
use App\Repositories\BaseRepository;

class InstallmentFeeRepository extends BaseRepository
{
    public function __construct(InstallmentFee $model)
    {
        parent::__construct($model);
    }

    public function insertMany(array $data): bool
    {
        return $this->model->insert($data);
    }

    public function updateOrRestore(array $data): InstallmentFee
    {
        $installment = $this->model->withTrashed()->findOrFail($data['id']);

        $this->update([
            'name' => $data['name'],
            'due_date' => date('Y-m-d', strtotime($data['due_date'])),
            'due_charges' => $data['due_charges']
        ], $installment->id);

        $installment->restore();

        return $installment;
    }
}