<?php

namespace App\Http\Requests\Exam;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class ExamSequenceRequest extends FormRequest {
    protected $stopOnFirstFailure = true;

    /**
     * Desequenceine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // This Condition will be called on Create Form
        if (request()->isMethod('GET')) {
            return Auth::user()->can('exam-sequence-list');
        }

        // This Condition will be called on Create Form
        if (request()->isMethod('POST')) {
            return Auth::user()->can('exam-sequence-create');
        }

        // This Condition will be called on Update Form
        if (request()->isMethod('PUT') || request()->isMethod('PATCH')) {
            return Auth::user()->can('exam-sequence-edit');
        }

        // This Condition will be called on Update Form
        if (request()->isMethod('DELETE')) {
            return Auth::user()->can('exam-sequence-delete');
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [];
        if (request()->isMethod('POST')) {
            // This Condition will be called on Create Form
            $rules = [
                'name'         => 'required',
                'exam_term_id' => 'required|numeric',
                'start_date'   => 'required|date',
                'end_date'     => 'required|date',
            ];
        } elseif (request()->isMethod('PUT')) {
            $rules = [
                'id'        => 'required',
                'name'       => 'required',
                'start_date' => 'required|date',
                'end_date'   => 'required|date',
            ];
        }
        return $rules;
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
