<?php

namespace App\Services;

use App\Contracts\QuestionRepositoryInterface;
use App\Contracts\QuestionServiceInterface;
use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class QuestionService implements QuestionServiceInterface
{
    public function __construct(
        private QuestionRepositoryInterface $questionRepository
    ) {}

    public function createQuestion(array $data): Question
    {
        $data['created_by_id'] = $data['created_by_id'] ?? auth()->id() ?? 1;

        return $this->questionRepository->create($data);
    }

    public function updateQuestion(Question $question, array $data): Question
    {
        return $this->questionRepository->update($question, $data);
    }

    public function deleteQuestion(Question $question): bool
    {
        return $this->questionRepository->delete($question);
    }

    public function getQuestionWithDetails(int $id): ?Question
    {
        return $this->questionRepository->findById($id);
    }

    public function getFilteredQuestions(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->questionRepository->getFilteredPaginated($filters, $perPage);
    }

    public function assignQuestionsToSurveys(array $questionIds, array $surveyIds, int $createdById): bool
    {
        if (empty($questionIds) || empty($surveyIds)) {
            return false;
        }

        return $this->questionRepository->assignToSurveys($questionIds, $surveyIds, $createdById);
    }

    public function bulkDeleteQuestions(array $questionIds): bool
    {
        if (empty($questionIds)) {
            return false;
        }

        // Get linked questions to exclude from deletion
        $linkedQuestions = $this->questionRepository->findLinkedQuestions($questionIds);
        $linkedQuestionIds = $linkedQuestions->pluck('id')->toArray();

        // Filter out linked questions from deletion list
        $deletableQuestionIds = array_diff($questionIds, $linkedQuestionIds);

        // If no questions can be deleted, return false
        if (empty($deletableQuestionIds)) {
            return false;
        }

        return $this->questionRepository->bulkDelete($deletableQuestionIds);
    }

    public function getQuestionsForMassAssignment(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->getFilteredQuestions($filters, $perPage);
    }

    public function getLinkedQuestions(array $questionIds): array
    {
        if (empty($questionIds)) {
            return [];
        }

        $linkedQuestions = $this->questionRepository->findLinkedQuestions($questionIds);

        return $linkedQuestions->pluck('name')->toArray();
    }

    public function getExistingQuestionsInSurveys(array $questionIds, array $surveyIds): array
    {
        if (empty($questionIds) || empty($surveyIds)) {
            return [];
        }

        $existingQuestions = $this->questionRepository->findExistingQuestionsInSurveys($questionIds, $surveyIds);

        return $existingQuestions->pluck('name')->toArray();
    }
}
