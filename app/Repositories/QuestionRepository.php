<?php

namespace App\Repositories;

use App\Contracts\QuestionRepositoryInterface;
use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class QuestionRepository implements QuestionRepositoryInterface
{
    public function create(array $data): Question
    {
        return Question::create($data);
    }

    public function update(Question $question, array $data): Question
    {
        $question->update($data);
        return $question->fresh();
    }

    public function delete(Question $question): bool
    {
        return $question->delete();
    }

    public function bulkDelete(array $questionIds): bool
    {
        if (empty($questionIds)) {
            return false;
        }

        // Delete questions by IDs (assuming they're already filtered for unlinkable questions)
        return Question::whereIn('id', $questionIds)->delete() > 0;
    }

    public function findById(int $id): ?Question
    {
        return Question::find($id);
    }

    public function getFilteredPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Question::with('createdBy');

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }
        if (!empty($filters['question_type'])) {
            $query->where('question_type', $filters['question_type']);
        }

        if (!empty($filters['created_by_id'])) {
            $query->where('created_by_id', $filters['created_by_id']);
        }
        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findByType(string $type): Collection
    {
        return Question::where('question_type', $type)->get();
    }

    public function findUnassignedQuestions(): Collection
    {
        return Question::doesntHave('surveys')->get();
    }

    public function assignToSurveys(array $questionIds, array $surveyIds, int $createdById): bool
    {
        try {
            DB::beginTransaction();

            $insertData = [];

            foreach ($surveyIds as $surveyId) {
                // Get existing question-survey combinations for this survey
                $existingQuestionIds = DB::table('question_survey')
                    ->where('survey_id', $surveyId)
                    ->whereIn('question_id', $questionIds)
                    ->pluck('question_id')
                    ->toArray();

                // Filter out questions that already exist in this survey
                $newQuestionIds = array_diff($questionIds, $existingQuestionIds);

                // Prepare insert data for new question-survey combinations
                foreach ($newQuestionIds as $questionId) {
                    $insertData[] = [
                        'survey_id' => $surveyId,
                        'question_id' => $questionId,
                        'created_by_id' => $createdById,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Insert only new combinations
            if (!empty($insertData)) {
                DB::table('question_survey')->insert($insertData);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function findLinkedQuestions(array $questionIds): Collection
    {
        return Question::whereIn('id', $questionIds)
            ->has('surveys')
            ->get();
    }

    public function findExistingQuestionsInSurveys(array $questionIds, array $surveyIds): Collection
    {
        return Question::whereIn('id', $questionIds)
            ->whereHas('surveys', function ($query) use ($surveyIds) {
                $query->whereIn('surveys.id', $surveyIds);
            })
            ->with(['surveys' => function ($query) use ($surveyIds) {
                $query->whereIn('id', $surveyIds);
            }])
            ->get();
    }
}
