<?php

namespace App\Http\Requests\Announcement;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::user()->can('announcement-create');
    }

    public function rules()
    {
        return [
            'title' => 'required',
            'description' => 'nullable',
            'set_data' => 'required|in:class_section,class,noticeboard',
            'get_data' => 'nullable|array',
            'class_section_id' => 'required_if:set_data,class_section',
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
