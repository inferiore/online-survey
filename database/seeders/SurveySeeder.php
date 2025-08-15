<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Database\Seeder;

class SurveySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        Survey::factory(5)->create(['created_by_id' => $user->id]);
        Survey::factory(3)->online()->create(['created_by_id' => $user->id]);
        Survey::factory(2)->finished()->create(['created_by_id' => $user->id]);
    }
}
