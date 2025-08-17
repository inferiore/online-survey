<?php

namespace App\Services;

use App\Contracts\SurveyRepositoryInterface;
use App\Contracts\SurveyServiceInterface;
use App\Models\Survey;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SurveyService implements SurveyServiceInterface
{
    public function __construct(
        private SurveyRepositoryInterface $surveyRepository
    ) {}

    public function createSurvey(array $data): Survey
    {
        $data['created_by_id'] = $data['created_by_id'] ?? auth()->id() ?? 1;
        
        return $this->surveyRepository->create($data);
    }

    public function updateSurvey(Survey $survey, array $data): Survey
    {
        return $this->surveyRepository->update($survey, $data);
    }

    public function deleteSurvey(Survey $survey): bool
    {
        return $this->surveyRepository->delete($survey);
    }

    public function getSurveyWithDetails(int $id): ?Survey
    {
        return $this->surveyRepository->findWithQuestions($id);
    }

    public function getFilteredSurveys(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->surveyRepository->getFilteredPaginated($filters, $perPage);
    }

    public function getSurveyForTaking(Survey $survey): ?Survey
    {
        if ($survey->status !== 'online') {
            return null;
        }

        $survey->load('questions');
        return $survey;
    }
}