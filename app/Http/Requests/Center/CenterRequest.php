<?php

namespace App\Http\Requests\Center;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CenterRequest extends FormRequest {
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        // This Condition will be called on Create Form
        if (request()->isMethod('GET')) {
            return Auth::user()->can('center-list');
        }

        // This Condition will be called on Create Form
        if (request()->isMethod('POST')) {
            return Auth::user()->can('center-create');
        }

        // This Condition will be called on Update Form
        if (request()->isMethod('PUT') || request()->isMethod('PATCH')) {
            return Auth::user()->can('center-edit');
        }

        // This Condition will be called on Update Form
        if (request()->isMethod('DELETE')) {
            return Auth::user()->can('center-delete');
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
                'name'                   => 'required',
                'email'                  => 'required|email',
                'contact'                => 'required',
                'logo'                   => 'required|mimes:jpg,jpeg,png',
                'tagline'                => 'required',
                'address'                => 'required',
                'user_first_name'        => 'required',
                //                'user_last_name'         => 'required',
                'user_email'             => 'required|email|unique:users,email',
                'user_contact'           => 'required',
                'user_dob'               => 'required|date',
                'user_gender'            => 'required|in:male,female',
                'user_image'             => 'required|mimes:jpg,jpeg,png',
                'user_current_address'   => 'required',
                'user_permanent_address' => 'required',
            ];
        } elseif (request()->isMethod('PUT')) {
            // This Condition will be called on Update Form
            $rules = [
                'id'                     => 'required|numeric',
                'name'                   => 'required',
                'email'                  => 'required|email',
                'contact'                => 'required',
                'logo'                   => 'nullable|mimes:jpg,jpeg,png',
                'tagline'                => 'required',
                'address'                => 'required',
                'user_first_name'        => 'required',
                'user_email'             => 'required|email|unique:users,email,' . request('user_id'),
                'user_contact'           => 'required',
                'user_dob'               => 'required|date',
                'user_gender'            => 'required|in:male,female',
                'user_current_address'   => 'required',
                'user_permanent_address' => 'required',
            ];
        }
        return $rules;
    }
}
