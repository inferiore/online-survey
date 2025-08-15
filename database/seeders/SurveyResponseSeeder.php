<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Database\Seeder;

class SurveyResponseSeeder extends Seeder
{
    public function run(): void
    {
        $surveys = Survey::with('questions')->where('status', 'online')->get();

        foreach ($surveys as $survey) {
            $responseCount = fake()->numberBetween(10, 50);
            
            for ($i = 0; $i < $responseCount; $i++) {
                $submittedAt = fake()->dateTimeBetween('-3 months', 'now');
                
                foreach ($survey->questions as $question) {
                    $factoryState = match($question->question_type) {
                        'rating' => 'forRatingQuestion',
                        'comment-only' => 'forCommentQuestion',
                        'multiple-choice' => 'forMultipleChoiceQuestion',
                        default => null
                    };

                    $factory = SurveyResponse::factory();
                    
                    if ($factoryState) {
                        $factory = $factory->{$factoryState}();
                    }

                    $factory->create([
                        'survey_id' => $survey->id,
                        'question_id' => $question->id,
                        'submitted_at' => $submittedAt,
                    ]);
                }
            }
        }
    }
}
