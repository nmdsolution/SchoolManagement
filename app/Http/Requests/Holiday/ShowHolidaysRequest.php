<?php

namespace App\Http\Requests\Holiday;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class ShowHolidaysRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->can('holiday-list');
    }

    public function rules(): array
    {
        return [
            'offset' => 'nullable|integer',
            'limit' => 'nullable|integer',
            'sort' => 'nullable|string',
            'order' => 'nullable|string|in:ASC,DESC,asc,desc',
            'search' => 'nullable|string',
            'print' => 'nullable|boolean'
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
