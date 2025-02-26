<?php

namespace App\Http\Requests\Exam;

use Illuminate\Foundation\Http\FormRequest;

class ShowExamResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('exam-result');
    }

    public function rules(): array
    {
        return [
            'exam_id' => 'required|exists:exams,id',
            'class_section_id' => 'nullable|exists:class_sections,id',
            'offset' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1',
            'sort' => 'nullable|string',
            'order' => 'nullable|string|in:ASC,DESC,asc,desc',
            'search' => 'nullable|string',
            'print' => 'nullable|boolean'
        ];
    }

    public function validated($key = null, $default = null): array
    {
        return array_merge([
            'offset' => 0,
            'limit' => 10,
            'sort' => 'id',
            'order' => 'DESC'
        ], parent::validated());
    }
}
