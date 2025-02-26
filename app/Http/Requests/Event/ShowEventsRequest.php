<?php

namespace App\Http\Requests\Event;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ShowEventsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('event-list');
    }

    public function rules(): array
    {
        return [
            'offset' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1',
            'sort' => 'nullable|string|in:id,name,start_date,end_date,location',
            'order' => 'nullable|string|in:ASC,DESC,asc,desc',
            'search' => 'nullable|string|max:100',
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
