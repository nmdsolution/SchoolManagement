<?php

namespace App\Http\Requests\Exam;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubmitExamMarksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exam_id' => 'required|integer|exists:exams,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'exam_marks' => 'required|array',
            'exam_marks.*.student_id' => 'required|integer|exists:students,id',
            'exam_marks.*.obtained_marks' => 'required|numeric_or_slash|lte:exam_marks.*.total_marks',
            'exam_marks.*.total_marks' => 'required|numeric|gt:0',
            'class_section_id' => 'required|integer|exists:class_sections,id',
            'marks_upload_status' => 'required|integer|in:0,1,2',
            'subject_competency' => 'nullable|string|max:50',
            'sequence_id' => 'nullable|integer|exists:exam_sequences,id'
        ];
    }

    public function messages(): array
    {
        return [
            'exam_marks.*.obtained_marks.required' => 'Obtained marks fields are required'
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
