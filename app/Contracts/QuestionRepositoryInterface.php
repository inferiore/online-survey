<?php

namespace App\Contracts;

use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface QuestionRepositoryInterface
{
    public function create(array $data): Question;

    public function update(Question $question, array $data): Question;

    public function delete(Question $question): bool;

    public function bulkDelete(array $questionIds): bool;

    public function findById(int $id): ?Question;

    public function getFilteredPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function findByType(string $type): Collection;

    public function findUnassignedQuestions(): Collection;

    public function assignToSurveys(array $questionIds, array $surveyIds, int $createdById): bool;

    public function findLinkedQuestions(array $questionIds): Collection;

    public function findExistingQuestionsInSurveys(array $questionIds, array $surveyIds): Collection;
}