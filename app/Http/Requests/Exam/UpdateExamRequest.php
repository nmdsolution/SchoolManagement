<?php

namespace App\Http\Requests\Exam;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateExamRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'edit_timetable'                 => 'required|array',
            'edit_timetable.*.subject_id'    => 'required',
            'edit_timetable.*.total_marks'   => 'nullable',
            'edit_timetable.*.passing_marks' => 'nullable|lte:edit_timetable.*.total_marks',
            'edit_timetable.*.start_time'    => 'nullable',
            'edit_timetable.*.date'          => 'nullable',
        ];

        // Vérifie si end_time existe et n'est pas égal à "00:00:00"
        if (!empty($this->edit_timetable[0]["end_time"]) && $this->edit_timetable[0]["end_time"] != "00:00:00") {
            $rules['edit_timetable.*.end_time'] = 'required|after:edit_timetable.*.start_time';
        }

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'edit_timetable.*.passing_marks.lte' => trans('passing_marks_should_less_than_or_equal_to_total_marks'),
            'edit_timetable.*.end_time.after'    => trans('end_time_should_be_greater_than_start_time')
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
