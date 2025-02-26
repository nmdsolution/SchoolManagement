<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FormFieldRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // This Condition will be called on Create Form
        if (request()->isMethod('GET')) {
            return Auth::user()->can('form-field-list');
        }

        // This Condition will be called on Create Form
        if (request()->isMethod('POST')) {
            return Auth::user()->can('form-field-create');
        }

        // This Condition will be called on Update Form
        if (request()->isMethod('PUT') || request()->isMethod('PATCH')) {
            return Auth::user()->can('form-field-edit');
        }

        // This Condition will be called on Update Form
        if (request()->isMethod('DELETE')) {
            return Auth::user()->can('form-field-delete');
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
                'name' => 'required',
                'type' => 'required|in:text,number,dropdown,radio,checkbox,textarea,file',
                'default_values' => 'required_if:type,dropdown,radio,checkbox|array',
                'is_required' => 'nullable|boolean',
            ];
        } elseif (request()->isMethod('PUT')) {
            // This Condition will be called on Update Form
            $rules = [
                'name' => 'required',
                'type' => 'required|in:text,number,dropdown,radio,checkbox,textarea,file',
                'default_values' => 'required_if:type,dropdown,radio,checkbox|array',
                'is_required' => 'nullable|boolean',
            ];
        }
        return $rules;
    }
}
