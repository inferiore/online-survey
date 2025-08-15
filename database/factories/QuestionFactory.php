<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $questionType = fake()->randomElement(['rating', 'comment-only', 'multiple-choice']);
        
        $questionsByType = [
            'rating' => [
                'How satisfied are you with our service?',
                'Rate the quality of our product',
                'How would you rate the customer support?',
                'Rate your overall experience',
                'How likely are you to recommend us?'
            ],
            'comment-only' => [
                'Please share your feedback',
                'What improvements would you suggest?',
                'Tell us about your experience',
                'Any additional comments?',
                'What did you like most?',
                'Please provide your email address'
            ],
            'multiple-choice' => [
                'Which feature do you use most?',
                'How did you hear about us?',
                'What is your preferred contact method?',
                'Which industry do you work in?',
                'What is your company size?'
            ]
        ];

        return [
            'name' => fake()->words(3, true) . ' Question',
            'question_text' => fake()->randomElement($questionsByType[$questionType]),
            'question_type' => $questionType,
            'created_by_id' => 1, // Default to first user, can be overridden
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function rating(): static
    {
        return $this->state(fn (array $attributes) => [
            'question_type' => 'rating',
            'question_text' => fake()->randomElement([
                'How satisfied are you with our service?',
                'Rate the quality of our product',
                'How would you rate the customer support?',
                'Rate your overall experience',
                'How likely are you to recommend us?'
            ]),
        ]);
    }

    public function commentOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'question_type' => 'comment-only',
            'question_text' => fake()->randomElement([
                'Please share your feedback',
                'What improvements would you suggest?',
                'Tell us about your experience',
                'Any additional comments?',
                'What did you like most?'
            ]),
        ]);
    }

    public function multipleChoice(): static
    {
        return $this->state(fn (array $attributes) => [
            'question_type' => 'multiple-choice',
            'question_text' => fake()->randomElement([
                'Which feature do you use most?',
                'How did you hear about us?',
                'What is your preferred contact method?',
                'Which industry do you work in?',
                'What is your company size?'
            ]),
        ]);
    }
}
