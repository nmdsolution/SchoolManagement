<?php

namespace App\Exports;

use App\Models\FormField;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RadioAndCheckboxData implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    public function collection()
    {
        //
        return FormField::select('name','default_values')->where('type','radio')->get();
    }

    public function headings(): array
    {
        return $data = ['name','value'];
    }
    
    public function title(): string
    {
        return 'Radio Button Values';
    }
    
}
