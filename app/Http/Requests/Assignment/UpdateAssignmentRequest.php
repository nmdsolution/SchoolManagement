<?php

namespace App\Http\Requests\Assignment;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('assignment-edit');
    }

    public function rules(): array
    {
        return [
            'class_section_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'name' => 'required',
            'instructions' => 'nullable',
            'due_date' => 'required|date',
            'points' => 'nullable',
            'resubmission' => 'nullable|boolean',
            'extra_days_for_resubmission' => 'nullable|numeric',
            'file.*' => 'nullable|file'
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
