<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyTakingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        User::factory()->create();

    }
    public function test_guest_can_view_online_survey()
    {

        $survey = Survey::factory()->online()->create();
        $questions = Question::factory(3)->create();
        $survey->questions()->attach($questions,['created_by_id'=>1]);

        $response = $this->get(route('surveys.take', $survey));

        $response->assertStatus(200);
        $response->assertViewIs('surveys.take');
        $response->assertViewHas('survey');
        $response->assertSee($survey->name);

        foreach ($questions as $question) {
            $response->assertSee($question->question_text);
        }
    }

    public function test_guest_cannot_view_created_survey()
    {
        $survey = Survey::factory()->created()->create();

        $response = $this->get(route('surveys.take', $survey));

        $response->assertStatus(200);
        $response->assertViewIs('surveys.not-available');
        $response->assertSee('Survey Not Available');
        $response->assertSee('Survey in Development');
    }

    public function test_guest_cannot_view_finished_survey()
    {
        $survey = Survey::factory()->finished()->create();

        $response = $this->get(route('surveys.take', $survey));

        $response->assertStatus(200);
        $response->assertViewIs('surveys.not-available');
        $response->assertSee('Survey Not Available');
        $response->assertSee('Survey Completed');
    }

    public function test_guest_can_submit_survey_responses()
    {
        $survey = Survey::factory()->online()->create();
        $questions = Question::factory(3)->create([
            'question_type' => 'comment-only'
        ]);
        $survey->questions()->attach($questions,['created_by_id'=>1]);

        $responses = [
            $questions[0]->id => 'Great service!',
            $questions[1]->id => 'Very satisfied with the experience.',
            $questions[2]->id => 'Would definitely recommend.'
        ];

        $response = $this->post(route('surveys.submit', $survey), [
            'responses' => $responses
        ]);

        $response->assertRedirect(route('surveys.take', $survey));
        $response->assertSessionHas('success');

        // Check that responses were saved
        $this->assertDatabaseCount('survey_responses', 3);

        foreach ($responses as $questionId => $value) {
            $this->assertDatabaseHas('survey_responses', [
                'survey_id' => $survey->id,
                'question_id' => $questionId,
                'response_value' => $value
            ]);
        }
    }

    public function test_survey_submission_requires_all_responses()
    {
        $survey = Survey::factory()->online()->create();
        $questions = Question::factory(3)->create();
        $survey->questions()->attach($questions,['created_by_id'=>1]);
        $response = $this->post(route('surveys.submit', $survey), [
            'responses' => [
                $questions[0]->id => 'Only one response',
                // Missing responses for other questions
            ]
        ]);
        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('survey_responses', 0);
    }

    public function test_cannot_submit_responses_to_non_online_survey()
    {
        $survey = Survey::factory()->created()->create();
        $question = Question::factory()->create();
        $survey->questions()->attach($question,['created_by_id'=>1]);
        $response = $this->post(route('surveys.submit', $survey), [
            'responses' => [
                $question->id => 'Test response'
            ]
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('surveys.not-available');
        $this->assertDatabaseCount('survey_responses', 0);
    }


    public function test_multiple_people_can_submit_same_survey()
    {
        $survey = Survey::factory()->online()->create();
        $question = Question::factory()->create();
        $survey->questions()->attach($question,['created_by_id'=>1]);

        // First submission
        $this->post(route('surveys.submit', $survey), [
            'responses' => [$question->id => 'First response']
        ]);

        // Second submission
        $this->post(route('surveys.submit', $survey), [
            'responses' => [$question->id => 'Second response']
        ]);

        $this->assertDatabaseCount('survey_responses', 2);

        $this->assertDatabaseHas('survey_responses', [
            'survey_id' => $survey->id,
            'question_id' => $question->id,
            'response_value' => 'First response'
        ]);

        $this->assertDatabaseHas('survey_responses', [
            'survey_id' => $survey->id,
            'question_id' => $question->id,
            'response_value' => 'Second response'
        ]);
    }

    public function test_survey_displays_different_question_types_correctly()
    {
        $survey = Survey::factory()->online()->create();

        $ratingQuestion = Question::factory()->rating()->create();
        $commentQuestion = Question::factory()->commentOnly()->create();
        $multipleChoiceQuestion = Question::factory()->multipleChoice()->create();

        $survey->questions()->attach([$ratingQuestion->id, $commentQuestion->id, $multipleChoiceQuestion->id],['created_by_id'=>1]);

        $response = $this->get(route('surveys.take', $survey));

        $response->assertStatus(200);

        // Check that different question types are rendered
        $response->assertSee($ratingQuestion->question_text);
        $response->assertSee($commentQuestion->question_text);
        $response->assertSee($multipleChoiceQuestion->question_text);

        // Check for rating scale elements
        $response->assertSee('1');
        $response->assertSee('5');

        // Check for textarea (comment questions)
        $response->assertSee('textarea');

        // Check for radio buttons (multiple choice)
        $response->assertSee('type="radio"',false);

    }

    public function test_survey_shows_success_message_after_submission()
    {
        $survey = Survey::factory()->online()->create();
        $question = Question::factory()->create();
        $survey->questions()->attach($question,['created_by_id'=>1]);

        $response = $this->post(route('surveys.submit', $survey), [
            'responses' => [$question->id => 'Test response']
        ]);

        $response->assertRedirect(route('surveys.take', $survey));
        $response->assertSessionHas('success', 'Thank you! Your survey response has been submitted successfully.');

        // Follow redirect and check success message is displayed
        $followResponse = $this->get(route('surveys.take', $survey));
        $followResponse->assertSee('Survey Submitted!');
        $followResponse->assertSee('Thank you for your valuable feedback');
    }
}
