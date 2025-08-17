<?php

namespace App\Contracts;

use App\Models\Survey;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SurveyRepositoryInterface
{
    public function create(array $data): Survey;

    public function update(Survey $survey, array $data): Survey;

    public function delete(Survey $survey): bool;

    public function findById(int $id): ?Survey;

    public function findWithQuestions(int $id): ?Survey;

    public function getFilteredPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function findAvailableSurveys(): Collection;

    public function findByStatus(string $status): Collection;
}