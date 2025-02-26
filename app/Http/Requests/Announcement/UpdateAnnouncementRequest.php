<?php

namespace App\Http\Requests\Announcement;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('announcement-edit');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:announcements,id',
            'title' => 'required',
            'description' => 'nullable',
            'set_data' => 'required|in:class_section,class,noticeboard',
            'get_data' => 'nullable',
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