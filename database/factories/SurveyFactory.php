<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Survey>
 */
class SurveyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $surveyTypes = [
            'Customer Satisfaction', 'Employee Feedback', 'Product Experience', 
            'Service Quality', 'Website Usability', 'Market Research',
            'Brand Awareness', 'Training Evaluation', 'Event Feedback'
        ];

        return [
            'name' => fake()->randomElement($surveyTypes) . ' ' . fake()->word() . ' Survey',
            'status' => fake()->randomElement(['created', 'online', 'finished']),
            'created_by_id' => 1, // Default to first user, can be overridden
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function created(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'created',
        ]);
    }

    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'online',
        ]);
    }

    public function finished(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'finished',
        ]);
    }
}
