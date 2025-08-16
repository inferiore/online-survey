<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_view_questions_index()
    {
        Question::factory(5)->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('questions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('questions.index');
        $response->assertViewHas('questions');
    }

    public function test_user_can_create_question()
    {
        $questionData = [
            'name' => 'Satisfaction Rating',
            'question_text' => 'How satisfied are you with our service?',
            'question_type' => 'rating'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('questions.store'), $questionData);

        $response->assertRedirect(route('questions.index'));
        $response->assertSessionHas('success', 'Question created successfully.');

        $this->assertDatabaseHas('questions', [
            'name' => 'Satisfaction Rating',
            'question_text' => 'How satisfied are you with our service?',
            'question_type' => 'rating',
            'created_by_id' => $this->user->id
        ]);
    }

    public function test_question_creation_requires_name()
    {
        $response = $this->actingAs($this->user)
            ->post(route('questions.store'), [
                'name' => '',
                'question_text' => 'Test question',
                'question_type' => 'rating'
            ]);

        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseCount('questions', 0);
    }

    public function test_question_creation_requires_question_text()
    {
        $response = $this->actingAs($this->user)
            ->post(route('questions.store'), [
                'name' => 'Test Question',
                'question_text' => '',
                'question_type' => 'rating'
            ]);

        $response->assertSessionHasErrors(['question_text']);
        $this->assertDatabaseCount('questions', 0);
    }

    public function test_question_creation_requires_valid_question_type()
    {
        $response = $this->actingAs($this->user)
            ->post(route('questions.store'), [
                'name' => 'Test Question',
                'question_text' => 'Test question text',
                'question_type' => 'invalid_type'
            ]);

        $response->assertSessionHasErrors(['question_type']);
        $this->assertDatabaseCount('questions', 0);
    }

    public function test_user_can_view_question_details()
    {
        $question = Question::factory()->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('questions.show', $question));

        $response->assertStatus(200);
        $response->assertViewIs('questions.show');
        $response->assertViewHas('question');
        $response->assertSee($question->name);
    }

    public function test_user_can_edit_question()
    {
        $question = Question::factory()->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('questions.edit', $question));

        $response->assertStatus(200);
        $response->assertViewIs('questions.edit');
        $response->assertViewHas('question');
    }

    public function test_user_can_update_question()
    {
        $question = Question::factory()->create(['created_by_id' => $this->user->id]);

        $updateData = [
            'name' => 'Updated Question Name',
            'question_text' => 'Updated question text?',
            'question_type' => 'multiple-choice'
        ];

        $response = $this->actingAs($this->user)
            ->put(route('questions.update', $question), $updateData);

        $response->assertRedirect(route('questions.index'));
        $response->assertSessionHas('success', 'Question updated successfully.');

        $this->assertDatabaseHas('questions', [
            'id' => $question->id,
            'name' => 'Updated Question Name',
            'question_text' => 'Updated question text?',
            'question_type' => 'multiple-choice'
        ]);
    }

    public function test_user_can_delete_question()
    {
        $question = Question::factory()->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('questions.destroy', $question));

        $response->assertRedirect(route('questions.index'));
        $response->assertSessionHas('success', 'Question deleted successfully.');

        $this->assertDatabaseMissing('questions', [
            'id' => $question->id
        ]);
    }

    public function test_user_can_filter_questions_by_name()
    {
        Question::factory()->create([
            'name' => 'Satisfaction Question',
            'created_by_id' => $this->user->id
        ]);
        Question::factory()->create([
            'name' => 'Feedback Question',
            'created_by_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('questions.index', ['name' => 'Satisfaction']));

        $response->assertStatus(200);
        $response->assertSee('Satisfaction Question');
        $response->assertDontSee('Feedback Question');
    }

    public function test_user_can_filter_questions_by_type()
    {
        Question::factory()->rating()->create(['created_by_id' => $this->user->id]);
        Question::factory()->commentOnly()->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('questions.index', ['question_type' => 'rating']));

        $response->assertStatus(200);
        $questions = $response->viewData('questions');

        foreach ($questions as $question) {
            $this->assertEquals('rating', $question->question_type);
        }
    }

    public function test_questions_are_paginated()
    {
        Question::factory(20)->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('questions.index'));

        $response->assertStatus(200);
        $questions = $response->viewData('questions');
        $this->assertCount(15, $questions); // Should show 15 per page
    }

    public function test_user_can_mass_assign_questions_to_surveys()
    {
        $questions = Question::factory(3)->create(['created_by_id' => $this->user->id]);
        $surveys = Survey::factory(2)->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->post(route('questions.mass-assign'), [
                'question_ids' => $questions->pluck('id')->toArray(),
                'survey_ids' => $surveys->pluck('id')->toArray()
            ]);

        $response->assertRedirect(route('questions.index'));
        $response->assertSessionHas('success', 'Questions assigned to surveys successfully.');

        // Check that all questions are assigned to all surveys
        foreach ($surveys as $survey) {
            foreach ($questions as $question) {
                $this->assertDatabaseHas('question_survey', [
                    'survey_id' => $survey->id,
                    'question_id' => $question->id
                ]);
            }
        }
    }

    public function test_user_can_mass_delete_questions()
    {
        $questions = Question::factory(3)->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('questions.mass-delete'), [
                'question_ids' => $questions->pluck('id')->toArray()
            ]);
        $noDeleteQuestions = Question::whereIn('id', $questions->pluck('id')->toArray())->has('surveys')->get()->pluck('name')->toArray();

        $message = count($noDeleteQuestions) >0 ? 'Questions partially deleted because some of them are linked to question or answers':'Questions deleted successfully.';

        $response->assertRedirect(route('questions.index'));
        $response->assertSessionHas('warning', $message);

        foreach ($questions as $question) {
            $this->assertDatabaseMissing('questions', [
                'id' => $question->id
            ]);
        }
    }

    public function test_mass_assign_requires_question_ids()
    {
        $surveys = Survey::factory(2)->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->post(route('questions.mass-assign'), [
                'survey_ids' => $surveys->pluck('id')->toArray()
            ]);

        $response->assertSessionHasErrors(['question_ids']);
    }

    public function test_mass_assign_requires_survey_ids()
    {
        $questions = Question::factory(2)->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->post(route('questions.mass-assign'), [
                'question_ids' => $questions->pluck('id')->toArray()
            ]);

        $response->assertSessionHasErrors(['survey_ids']);
    }
}
