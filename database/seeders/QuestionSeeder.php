<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        Question::factory(7)->rating()->create(['created_by_id' => $user->id]);
        Question::factory(6)->commentOnly()->create(['created_by_id' => $user->id]);
        Question::factory(7)->multipleChoice()->create(['created_by_id' => $user->id]);

        $this->assignQuestionsToSurveys($user);
    }

    private function assignQuestionsToSurveys(User $user): void
    {
        $surveys = Survey::all();
        $questions = Question::all();

        if ($surveys->count() > 0 && $questions->count() > 0) {
            foreach ($surveys as $survey) {
                $randomQuestions = $questions->random(rand(2, 4));
                foreach ($randomQuestions as $question) {
                    $survey->questions()->syncWithoutDetaching([
                        $question->id => ['created_by_id' => $user->id]
                    ]);
                }
            }
        }
    }
}
