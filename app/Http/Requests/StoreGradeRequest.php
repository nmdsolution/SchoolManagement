<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('grade-create');
    }

    public function rules(): array
    {
        return [
            'grade' => 'required|array',
            'grade.*.id' => 'nullable|exists:grades,id',
            'grade.*.starting_range' => 'required|numeric|between:0,100',
            'grade.*.ending_range' => [
                'required',
                'numeric',
                'between:0,100',
                'gt:grade.*.starting_range'
            ],
            'grade.*.grades' => 'required|string',
            'grade.*.remarks' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'grade.*.starting_range.between' => 'Le rang de début doit être entre 0 et 100',
            'grade.*.ending_range.between' => 'Le rang de fin doit être entre 0 et 100',
            'grade.*.ending_range.gt' => 'Le rang de fin doit être supérieur au rang de début'
        ];
    }
}
