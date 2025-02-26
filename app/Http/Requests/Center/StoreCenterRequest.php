<?php

namespace App\Http\Requests\Center;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreCenterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->can('center-create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_first_name' => 'required|string',
            'user_gender' => 'required|string',
            'user_current_address' => 'required|string',
            'user_permanent_address' => 'required|string',
            'user_email' => 'required|email|unique:users,email',
            'user_contact' => 'required|string',
            'user_dob' => 'required|date',
            'user_image' => 'nullable|image',
            'name' => 'required|string',
            'email' => 'required|email',
            'contact' => 'required|string',
            'logo' => 'nullable|image',
            'tagline' => 'nullable|string',
            'address' => 'required|string',
            'type' => 'required|string'
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
