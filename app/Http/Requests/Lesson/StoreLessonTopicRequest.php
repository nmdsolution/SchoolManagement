<?php

namespace App\Http\Requests\Lesson;

use App\Rules\uniqueTopicInLesson;
use App\Rules\YouTubeUrl;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreLessonTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->can('topic-create');
    }

    public function rules(): array
    {
        return [
            'class_section_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'lesson_id' => 'required|numeric',
            'name' => [
                'required',
                new uniqueTopicInLesson($this->lesson_id)
            ],
            'description' => 'required',
            'file' => 'nullable|array',
            'file.*.type' => 'nullable|in:file_upload,youtube_link,video_upload,other_link',
            'file.*.name' => 'required_with:file.*.type',
            'file.*.thumbnail' => 'required_if:file.*.type,youtube_link,video_upload,other_link',
            'file.*.file' => 'required_if:file.*.type,file_upload,video_upload',
            'file.*.link' => [
                'nullable',
                'required_if:file.*.type,youtube_link',
                new YouTubeUrl
            ]
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
