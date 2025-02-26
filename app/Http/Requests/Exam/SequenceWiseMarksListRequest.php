<?php

namespace App\Http\Requests\Exam;

use Illuminate\Foundation\Http\FormRequest;

class SequenceWiseMarksListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'offset' => 'nullable|integer',
            'limit' => 'nullable|integer',
            'sort' => 'nullable|string',
            'order' => 'nullable|string',
            'class_section_id' => 'nullable|integer',
            'sequence_id' => 'required|integer',
            'search' => 'nullable|string',
        ];
    }
}
