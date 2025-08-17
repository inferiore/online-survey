<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'required|in:created,online,finished',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The survey name is required.',
            'name.max' => 'The survey name may not be greater than 255 characters.',
            'status.required' => 'The survey status is required.',
            'status.in' => 'The survey status must be one of: created, online, finished.',
        ];
    }
}