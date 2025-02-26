<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StudentDummayFile implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Student Data' => new StudentSampleFile(),
            'Radio Button Values' => new RadioAndCheckboxData(),
            'Checkbox Values' => new CheckboxData()
        ];
    }
}
