<?php

namespace App\Exports;

use App\Models\FormField;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class StudentSampleFile implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect([]);
    }
    public function headings(): array
    {
        // Define your column names here

        $column = [ 'full_name', 'gender', 'dob', 'born_at', 'minisec_matricule', 'admission_date', 'guardian', 'father_email', 'father_full_name', 'father_mobile', 'father_dob', 'father_occupation', 'mother_email', 'mother_full_name', 'mother_mobile', 'mother_dob', 'mother_occupation', 'guardian_email', 'guardian_full_name', 'guardian_mobile', 'guardian_gender', 'guardian_dob', 'guardian_occupation' ];
        $form_field = FormField::where('center_id',get_center_id())->whereNot('type','file')->get()->pluck('name')->toArray();
        $insert_index = 4;
        $form_field = array_map('strtolower', $form_field);
        array_splice($column, $insert_index, 0, $form_field);
        return $column;
        
    }

    public function title(): string
    {
        return 'Student Data';
    }
}
