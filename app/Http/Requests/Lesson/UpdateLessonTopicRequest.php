<?php

namespace App\Http\Requests\Lesson;

use App\Rules\uniqueTopicInLesson;
use App\Rules\YouTubeUrl;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UpdateLessonTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->can('topic-edit');
    }

    public function rules(): array
    {
        return [
            'edit_id' => 'required|numeric',
            'class_section_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'name' => [
                'required',
                new uniqueTopicInLesson($this->lesson_id, $this->edit_id)
            ],
            'description' => 'required',
            
            'edit_file' => 'nullable|array',
            'edit_file.*.id' => 'required|exists:files,id',
            'edit_file.*.type' => 'nullable|in:file_upload,youtube_link,video_upload,other_link',
            'edit_file.*.name' => 'nullable|required_with:edit_file.*.type',
            'edit_file.*.link' => [
                'nullable',
                'required_if:edit_file.*.type,youtube_link',
                new YouTubeUrl
            ],

            'file' => 'nullable|array',
            'file.*.type' => 'nullable|in:file_upload,youtube_link,video_upload,other_link',
            'file.*.name' => 'nullable|required_with:file.*.type',
            'file.*.thumbnail' => 'nullable|required_if:file.*.type,youtube_link,video_upload,other_link',
            'file.*.file' => 'nullable|required_if:file.*.type,file_upload,video_upload',
            'file.*.link' => [
                'nullable',
                'required_if:file.*.type,youtube_link',
                new YouTubeUrl
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => trans('topic_alredy_exists')
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
