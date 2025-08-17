<?php

namespace App\Contracts;

use App\Models\Survey;

interface SurveyResponseServiceInterface
{
    public function validateSurveyAvailability(Survey $survey): bool;

    public function submitResponses(Survey $survey, array $responses): bool;

    public function mapResponsesToDatabase(Survey $survey, array $responses): array;

    public function countResponsesForSurvey(int $surveyId): int;
}