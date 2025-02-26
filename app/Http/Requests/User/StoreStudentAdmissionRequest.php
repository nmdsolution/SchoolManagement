<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreStudentAdmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'first_name' => 'required',
            'image' => 'mimes:jpeg,png,jpg|image|max:2048',
            'dob' => 'required',
            'admission_date' => 'required',
            'father_email' => 'email|nullable',
            'nationality' => 'required',
            'repeater' => 'nullable|boolean',
            'born_at' => 'required|string',
            'status' => 'required',
        ];

        if (auth()->user()->center->type === 'primary') {
            $rules['class_id'] = 'required|exists:classes,id';
        } else {
            $rules['class_section_id'] = 'required|exists:class_sections,id';
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
