<?php

namespace App\Contracts;

use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface QuestionServiceInterface
{
    public function createQuestion(array $data): Question;

    public function updateQuestion(Question $question, array $data): Question;

    public function deleteQuestion(Question $question): bool;

    public function getQuestionWithDetails(int $id): ?Question;

    public function getFilteredQuestions(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function assignQuestionsToSurveys(array $questionIds, array $surveyIds, int $createdById): bool;

    public function bulkDeleteQuestions(array $questionIds): bool;

    public function getQuestionsForMassAssignment(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function getLinkedQuestions(array $questionIds): array;

    public function getExistingQuestionsInSurveys(array $questionIds, array $surveyIds): array;
}