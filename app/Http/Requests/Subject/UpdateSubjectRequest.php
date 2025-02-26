<?php

namespace App\Http\Requests\Subject;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('subjects')
                    ->where('name', $this->name)
                    ->where('type', $this->type)
                    ->where('center_id', auth()->user()->center->id)
                    ->ignore($this->route('id'))
            ],
            'type' => 'required|in:Practical,Theory',
            'bg_color' => 'required',
            'image' => 'nullable|mimes:jpeg,png,jpg,svg|image|max:2048',
            'code' => 'nullable'
        ];
    }

    public function attributes(): array
    {
        return [
            'bg_color' => 'Background Color'
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
