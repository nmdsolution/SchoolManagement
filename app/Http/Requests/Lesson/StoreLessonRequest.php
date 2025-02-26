<?php

namespace App\Http\Requests\Lesson;

use App\Rules\uniqueLessonInClass;
use App\Rules\YouTubeUrl;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->can('lesson-create');
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                new uniqueLessonInClass($this->class_section_id, $this->subject_id)
            ],
            'description' => 'required',
            'class_section_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'file' => 'nullable|array',
            'file.*.type' => 'nullable|in:file_upload,youtube_link,video_upload,other_link',
            'file.*.name' => 'required_with:file.*.type',
            'file.*.thumbnail' => 'required_if:file.*.type,youtube_link,video_upload,other_link',
            'file.*.file' => 'required_if:file.*.type,file_upload,video_upload',
            'file.*.link' => [
                'required_if:file.*.type,youtube_link',
                new YouTubeUrl,
                'nullable'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => trans('lesson_alredy_exists')
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
