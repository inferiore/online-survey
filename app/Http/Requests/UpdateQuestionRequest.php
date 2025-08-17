<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'question_text' => 'required|string',
            'question_type' => 'required|in:rating,comment-only,multiple-choice',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The question name is required.',
            'question_text.required' => 'The question text is required.',
            'question_type.required' => 'The question type is required.',
            'question_type.in' => 'The question type must be one of: rating, comment-only, multiple-choice.',
        ];
    }
}
