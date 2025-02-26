<?php

namespace App\Http\Requests\Course;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UpdateCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return Auth::user()->can('course-edit');
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'price' => 'required',
            'duration' => 'required',
            'super_teacher_ids.*' => 'required',
            'description' => 'required',
            'file' => 'mimes:mp4,ppx,ppt,pptx,pdf,ogv,jpeg,jpg,webm,xlxs,csv,tsv,xls',
            'thumbnail' => 'nullable|image',
            'category_id' => 'nullable',
            'tags' => 'nullable',
            'old_files' => 'nullable|array',
            'course_section' => 'nullable|array'
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
