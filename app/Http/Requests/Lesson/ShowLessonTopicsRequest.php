<?php

namespace App\Http\Requests\Lesson;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ShowLessonTopicsRequest extends FormRequest
{
    public function authorize(): bool
   {
       return $this->user()->can('topic-list');
   }

   public function rules(): array
   {
       return [
           'offset' => 'nullable|integer|min:0',
           'limit' => 'nullable|integer|min:1',
           'sort' => 'nullable|string',
           'order' => 'nullable|string|in:ASC,DESC,asc,desc',
           'search' => 'nullable|string|max:100',
           'subject_id' => 'nullable|exists:subjects,id',
           'class_id' => 'nullable|exists:class_sections,id',
           'lesson_id' => 'nullable|exists:lessons,id'
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

   public function messages(): array
   {
       return [
           'offset.min' => 'La valeur de offset doit être positive',
           'limit.min' => 'La limite doit être supérieure à 0',
           'sort.in' => 'Le champ de tri n\'est pas valide',
           'order.in' => 'L\'ordre de tri doit être ASC ou DESC',
           'search.max' => 'La recherche ne peut pas dépasser 100 caractères',
           'subject_id.exists' => 'Le sujet sélectionné n\'existe pas',
           'class_id.exists' => 'La classe sélectionnée n\'existe pas',
           'lesson_id.exists' => 'La leçon sélectionnée n\'existe pas'
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
