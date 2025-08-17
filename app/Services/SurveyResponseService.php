<?php

namespace App\Services;

use App\Contracts\SurveyResponseServiceInterface;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Support\Facades\DB;

class SurveyResponseService implements SurveyResponseServiceInterface
{
    public function validateSurveyAvailability(Survey $survey): bool
    {
        return $survey->status === 'online';
    }

    public function submitResponses(Survey $survey, array $responses): bool
    {
        if (!$this->validateSurveyAvailability($survey)) {
            return false;
        }

        try {
            DB::beginTransaction();

            $survey->load('questions');
            $mappedResponses = $this->mapResponsesToDatabase($survey, $responses);
            
            SurveyResponse::insert($mappedResponses);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function mapResponsesToDatabase(Survey $survey, array $responses): array
    {
        $submittedAt = now();
        
        return $survey->questions->map(function ($question) use ($responses, $survey, $submittedAt) {
            return [
                'survey_id' => $survey->id,
                'question_id' => $question->id,
                'response_value' => $responses['responses'][$question->id] ?? '',
                'submitted_at' => $submittedAt,
                'created_at' => $submittedAt,
                'updated_at' => $submittedAt,
            ];
        })->toArray();
    }

    public function countResponsesForSurvey(int $surveyId): int
    {
        return SurveyResponse::where('survey_id', $surveyId)
            ->distinct('submitted_at')
            ->count();
    }
}