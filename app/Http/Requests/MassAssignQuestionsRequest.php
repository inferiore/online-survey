<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MassAssignQuestionsRequest extends FormRequest
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
            'survey_ids' => 'required|array|min:1',
            'survey_ids.*' => 'required|integer|exists:surveys,id',
        ];
    }

    public function messages(): array
    {
        return [
            'question_ids.required' => 'Please select at least one question.',
            'question_ids.array' => 'Invalid question selection format.',
            'question_ids.min' => 'Please select at least one question.',
            'question_ids.*.exists' => 'One or more selected questions do not exist.',
            'survey_ids.required' => 'Please select at least one survey.',
            'survey_ids.array' => 'Invalid survey selection format.',
            'survey_ids.min' => 'Please select at least one survey.',
            'survey_ids.*.exists' => 'One or more selected surveys do not exist.',
        ];
    }
}