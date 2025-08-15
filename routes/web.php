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
Route::resource('questions', QuestionController::class);

// Mass update routes for questions
Route::post('questions/mass-assign', [QuestionController::class, 'massAssign'])->name('questions.mass-assign');
Route::delete('questions/mass-delete', [QuestionController::class, 'massDelete'])->name('questions.mass-delete');
