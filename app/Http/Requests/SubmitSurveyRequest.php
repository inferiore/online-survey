<?php

namespace App\Http\Requests;

use App\Models\Survey;
use Illuminate\Foundation\Http\FormRequest;

class SubmitSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $survey = $this->route('survey');
        
        if (!$survey instanceof Survey) {
            return ['responses' => 'required|array'];
        }

        $survey->load('questions');
        $rules = ['responses' => 'required|array|min:1'];

        foreach ($survey->questions as $question) {
            $rules["responses.{$question->id}"] = 'required|string|max:1000';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'responses.required' => 'Please provide responses to all questions.',
            'responses.array' => 'Invalid response format.',
            'responses.min' => 'Please answer at least one question.',
            'responses.*.required' => 'This question requires an answer.',
            'responses.*.string' => 'Please provide a valid response.',
            'responses.*.max' => 'Response must not exceed 1000 characters.',
        ];
    }
}