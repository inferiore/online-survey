<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Survey;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if(User::all()->count() === 0) {
            $this->call(UserSeeder::class);
        }
        if(Survey::all()->count() === 0) {
            $this->call(SurveySeeder::class);
        }
        if(Question::all()->count() === 0) {
            $this->call(QuestionSeeder::class);
        }
        if(User::all()->count() === 0) {
            $this->call(UserSeeder::class);
        }
    }
}
