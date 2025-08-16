<?php

namespace Tests\Feature;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_view_surveys_index()
    {
        Survey::factory(3)->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('surveys.index'));

        $response->assertStatus(200);
        $response->assertViewIs('surveys.index');
        $response->assertViewHas('surveys');
    }

    public function test_user_can_create_survey()
    {
        $surveyData = [
            'name' => 'Customer Satisfaction Survey',
            'status' => 'created'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('surveys.store'), $surveyData);

        $response->assertRedirect(route('surveys.index'));
        $response->assertSessionHas('success', 'Survey created successfully.');
        
        $this->assertDatabaseHas('surveys', [
            'name' => 'Customer Satisfaction Survey',
            'status' => 'created',
            'created_by_id' => $this->user->id
        ]);
    }

    public function test_survey_creation_requires_name()
    {
        $response = $this->actingAs($this->user)
            ->post(route('surveys.store'), [
                'name' => '',
                'status' => 'created'
            ]);

        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseCount('surveys', 0);
    }

    public function test_survey_creation_requires_valid_status()
    {
        $response = $this->actingAs($this->user)
            ->post(route('surveys.store'), [
                'name' => 'Test Survey',
                'status' => 'invalid_status'
            ]);

        $response->assertSessionHasErrors(['status']);
        $this->assertDatabaseCount('surveys', 0);
    }

    public function test_user_can_view_survey_details()
    {
        $survey = Survey::factory()->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('surveys.show', $survey));

        $response->assertStatus(200);
        $response->assertViewIs('surveys.show');
        $response->assertViewHas('survey');
        $response->assertSee($survey->name);
    }

    public function test_user_can_edit_survey()
    {
        $survey = Survey::factory()->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('surveys.edit', $survey));

        $response->assertStatus(200);
        $response->assertViewIs('surveys.edit');
        $response->assertViewHas('survey');
    }

    public function test_user_can_update_survey()
    {
        $survey = Survey::factory()->create(['created_by_id' => $this->user->id]);

        $updateData = [
            'name' => 'Updated Survey Name',
            'status' => 'online'
        ];

        $response = $this->actingAs($this->user)
            ->put(route('surveys.update', $survey), $updateData);

        $response->assertRedirect(route('surveys.index'));
        $response->assertSessionHas('success', 'Survey updated successfully.');
        
        $this->assertDatabaseHas('surveys', [
            'id' => $survey->id,
            'name' => 'Updated Survey Name',
            'status' => 'online'
        ]);
    }

    public function test_user_can_delete_survey()
    {
        $survey = Survey::factory()->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('surveys.destroy', $survey));

        $response->assertRedirect(route('surveys.index'));
        $response->assertSessionHas('success', 'Survey deleted successfully.');
        
        $this->assertDatabaseMissing('surveys', [
            'id' => $survey->id
        ]);
    }

    public function test_user_can_filter_surveys_by_name()
    {
        Survey::factory()->create([
            'name' => 'Customer Survey',
            'created_by_id' => $this->user->id
        ]);
        Survey::factory()->create([
            'name' => 'Employee Survey',
            'created_by_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('surveys.index', ['name' => 'Customer']));

        $response->assertStatus(200);
        $response->assertSee('Customer Survey');
        $response->assertDontSee('Employee Survey');
    }

    public function test_user_can_filter_surveys_by_status()
    {
        Survey::factory()->online()->create(['created_by_id' => $this->user->id]);
        Survey::factory()->finished()->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('surveys.index', ['status' => 'online']));

        $response->assertStatus(200);
        $surveys = $response->viewData('surveys');
        
        foreach ($surveys as $survey) {
            $this->assertEquals('online', $survey->status);
        }
    }

    public function test_surveys_are_paginated()
    {
        Survey::factory(15)->create(['created_by_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('surveys.index'));

        $response->assertStatus(200);
        $surveys = $response->viewData('surveys');
        $this->assertCount(10, $surveys); // Should show 10 per page
    }
}
