<?php

namespace App\Http\Requests\SessionYear;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSessionYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('session-year-create');
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'fees_due_date' => 'required|date|after_or_equal:start_date|before_or_equal:end_date',
            'fees_due_charges' => 'required|numeric|gt:0',
            'fees_installment' => 'required|boolean',
            'installment_data' => 'required_if:fees_installment,1|array',
            'installment_data.*.name' => 'required_if:fees_installment,1|string',
            'installment_data.*.due_date' => [
                'required_if:fees_installment,1',
                'date',
                'after_or_equal:start_date',
                'before_or_equal:end_date'
            ],
            'installment_data.*.due_charges' => 'required_if:fees_installment,1|numeric|gt:0'
        ];
    }

    public function messages(): array
    {
        return [
            'installment_data.*.name.required_if' => trans('name_is_required_at_row') . ' :index',
            'installment_data.*.due_date.required_if' => trans('name_is_required_at_row') . ' :index',
            'installment_data.*.due_date.date' => trans('due_date_should_be_date_at_row') . ' :index',
            'installment_data.*.due_date.after_or_equal' => trans('due_date_should_be_after_or_equal_session_year_start_date_at_row') . ' :index',
            'installment_data.*.due_date.before_or_equal' => trans('due_date_should_be_before_or_equal_session_year_end_date_at_row') . ' :index',
            'installment_data.*.due_charges.required_if' => trans('due_charges_required_at_row') . ' :index',
            'installment_data.*.due_charges.numeric' => trans('due_charges_should_be_number_at_row') . ' :index'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'start_date' => date('Y/m/d', strtotime($this->start_date)),
            'end_date' => date('Y/m/d', strtotime($this->end_date)),
            'fees_due_date' => date('Y-m-d', strtotime($this->fees_due_date))
        ]);
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
