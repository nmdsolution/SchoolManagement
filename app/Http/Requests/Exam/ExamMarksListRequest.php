<?php

namespace App\Http\Requests\Exam;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExamMarksListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('exam-upload-marks');
    }

    public function rules(): array
    {
        return [
            'exam_id' => 'required|integer|exists:exams,id',
            'class_section_id' => 'required|integer|exists:class_sections,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'sort' => 'nullable|string|in:student_name,id,created_at',
            'order' => 'nullable|string|in:ASC,DESC,asc,desc',
            'search' => 'nullable|string|max:100'
        ];
    }

    public function validated($key = null, $default = null): array
    {
        return array_merge([
            'sort' => 'users.first_name',
            'order' => 'DESC'
        ], parent::validated());
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'error' => true,
                'message' => $validator->errors()->first()
            ])
        );
    }
}
