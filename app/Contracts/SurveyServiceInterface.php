<?php

namespace App\Contracts;

use App\Models\Survey;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SurveyServiceInterface
{
    public function createSurvey(array $data): Survey;

    public function updateSurvey(Survey $survey, array $data): Survey;

    public function deleteSurvey(Survey $survey): bool;

    public function getSurveyWithDetails(int $id): ?Survey;

    public function getFilteredSurveys(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function getSurveyForTaking(Survey $survey): ?Survey;
}