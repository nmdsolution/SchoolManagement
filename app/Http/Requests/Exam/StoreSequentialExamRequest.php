<?php

namespace App\Http\Requests\Exam;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSequentialExamRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'class_section_id'      => 'required',
            'description'           => 'nullable',
            'timetable_subject_id'  => 'required|numeric|exists:subjects,id',
            'exam_term_id'          => 'required_if:type,1',
            'exam_sequence_id'      => 'required_if:type,1',
        ];
    }

    public function authorize(): bool
    {
        return true;
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
