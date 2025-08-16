<?php

use App\Http\Controllers\SurveyController;
use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Survey routes
Route::resource('surveys', SurveyController::class);

// Question routes

// Mass update routes for questions
Route::get('questions/mass-assign', [QuestionController::class, 'showMassAssign'])->name('questions.mass-assign.form');
Route::resource('questions', QuestionController::class);
Route::post('questions/mass-assign', [QuestionController::class, 'massAssign'])->name('questions.mass-assign');
Route::delete('questions/mass-delete', [QuestionController::class, 'massDelete'])->name('questions.mass-delete');

// Public survey routes (for taking surveys)
Route::get('take-survey/{survey}', [SurveyController::class, 'takeSurvey'])->name('surveys.take');
Route::post('submit-survey/{survey}', [SurveyController::class, 'submitSurvey'])->name('surveys.submit');
