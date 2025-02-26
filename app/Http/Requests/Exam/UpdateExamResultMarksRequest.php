<?php

namespace App\Http\Requests\Exam;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateExamResultMarksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('exam-result');
    }

    public function rules(): array
    {
        return [
            'edit' => 'required|array',
            'edit.*.marks_id' => 'required|integer|exists:exam_marks,id',
            'edit.*.obtained_marks' => 'required|numeric|lte:edit.*.total_marks',
            'edit.*.total_marks' => 'required|numeric|gt:0',
            'edit.*.passing_marks' => 'required|numeric',
            'edit.*.exam_id' => 'required|exists:exams,id',
            'edit.*.student_id' => 'required|exists:students,id'
        ];
    }

    public function messages(): array
    {
        return [
            'edit.*.obtained_marks.lte' => 'Les notes obtenues ne peuvent pas dépasser le total'
        ];
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
