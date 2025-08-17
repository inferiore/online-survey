<?php

namespace App\Repositories;

use App\Contracts\SurveyRepositoryInterface;
use App\Models\Survey;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SurveyRepository implements SurveyRepositoryInterface
{
    public function create(array $data): Survey
    {
        return Survey::create($data);
    }

    public function update(Survey $survey, array $data): Survey
    {
        $survey->update($data);
        return $survey->fresh();
    }

    public function delete(Survey $survey): bool
    {
        return $survey->delete();
    }

    public function findById(int $id): ?Survey
    {
        return Survey::find($id);
    }

    public function findWithQuestions(int $id): ?Survey
    {
        return Survey::with(['questions', 'createdBy'])->find($id);
    }

    public function getFilteredPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Survey::with('createdBy');

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['created_by_id'])) {
            $query->where('created_by_id', $filters['created_by_id']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findAvailableSurveys(): Collection
    {
        return Survey::where('status', 'online')->get();
    }

    public function findByStatus(string $status): Collection
    {
        return Survey::where('status', $status)->get();
    }
}