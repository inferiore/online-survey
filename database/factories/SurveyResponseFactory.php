<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SurveyResponse>
 */
class SurveyResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'survey_id' => 1, // Will be overridden
            'question_id' => 1, // Will be overridden
            'response_value' => fake()->sentence(),
            'submitted_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }

    public function forRatingQuestion(): static
    {
        return $this->state(fn (array $attributes) => [
            'response_value' => (string) fake()->numberBetween(1, 5),
        ]);
    }

    public function forCommentQuestion(): static
    {
        return $this->state(fn (array $attributes) => [
            'response_value' => fake()->boolean(20) 
                ? fake()->email() 
                : fake()->sentence(fake()->numberBetween(5, 20)),
        ]);
    }

    public function forMultipleChoiceQuestion(): static
    {
        $options = [
            'Very Likely', 'Likely', 'Neutral', 'Unlikely', 'Very Unlikely',
            'Option A', 'Option B', 'Option C', 'Option D',
            'Yes', 'No', 'Maybe',
            'Excellent', 'Good', 'Fair', 'Poor'
        ];

        return $this->state(fn (array $attributes) => [
            'response_value' => fake()->randomElement($options),
        ]);
    }
}
