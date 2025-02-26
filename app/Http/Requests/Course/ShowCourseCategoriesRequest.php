<?php

namespace App\Http\Requests\Course;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ShowCourseCategoriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('course-list');
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:100',
            'offset' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1',
            'order' => 'nullable|string|in:ASC,DESC,asc,desc'
        ];
    }

    public function validated($key = null, $default = null): array
    {
        return array_merge([
            'offset' => 0,
            'limit' => 10,
            'sort' => 'id',
            'order' => 'DESC'
        ], parent::validated());
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
