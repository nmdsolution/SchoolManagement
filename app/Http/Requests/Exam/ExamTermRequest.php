<?php

namespace App\Http\Requests\Exam;

use App\Rules\uniqueForCenter;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class ExamTermRequest extends FormRequest {
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        // This Condition will be called on Create Form
        if (request()->isMethod('GET')) {
            return Auth::user()->can('exam-term-list');
        }

        // This Condition will be called on Create Form
        if (request()->isMethod('POST')) {
            return Auth::user()->can('exam-term-create');
        }

        // This Condition will be called on Update Form
        if (request()->isMethod('PUT') || request()->isMethod('PATCH')) {
            return Auth::user()->can('exam-term-edit');
        }

        // This Condition will be called on Update Form
        if (request()->isMethod('DELETE')) {
            return Auth::user()->can('exam-term-delete');
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array {
        $rules = [];
        if (request()->isMethod('POST')) {
            // This Condition will be called on Create Form
            $rules = [
                'name'          => ['required', new uniqueForCenter('exam_terms', 'name')],
                'sequence_name' => 'array|nullable',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date'
            ];
        } elseif (request()->isMethod('PUT')) {
            // This Condition will be called on Update Form
            $rules = [
                'name' => ['required', new uniqueForCenter('exam_terms', 'name', request('id'))],
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date'
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
