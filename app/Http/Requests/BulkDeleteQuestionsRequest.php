<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteQuestionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'required|integer|exists:questions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'question_ids.required' => 'Please select at least one question to delete.',
            'question_ids.array' => 'Invalid question selection format.',
            'question_ids.min' => 'Please select at least one question to delete.',
            'question_ids.*.exists' => 'One or more selected questions do not exist.',
        ];
    }
}