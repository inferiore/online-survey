<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index(Request $request)
    {
        $query = Survey::with('createdBy');

        // Filter by name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $surveys = $query->latest()->paginate(10)->withQueryString();

        return view('surveys.index', compact('surveys'));
    }

    public function create()
    {
        return view('surveys.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:created,online,finished',
        ]);

        Survey::create([
            'name' => $request->name,
            'status' => $request->status,
            'created_by_id' => auth()->id()??1,
        ]);

        return redirect()->route('surveys.index')->with('success', 'Survey created successfully.');
    }

    public function show(Survey $survey)
    {
        $survey->load('questions', 'createdBy');
        return view('surveys.show', compact('survey'));
    }

    public function edit(Survey $survey)
    {
        return view('surveys.edit', compact('survey'));
    }

    public function update(Request $request, Survey $survey)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:created,online,finished',
        ]);

        $survey->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('surveys.index')->with('success', 'Survey updated successfully.');
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();
        return redirect()->route('surveys.index')->with('success', 'Survey deleted successfully.');
    }

    public function takeSurvey(Survey $survey)
    {
        if ($survey->status !== 'online') {
            return view('surveys.not-available', compact('survey'));
        }

        $survey->load('questions');
        return view('surveys.take', compact('survey'));
    }

    public function submitSurvey(Request $request, Survey $survey)
    {
        if ($survey->status !== 'online') {
            return view('surveys.not-available', compact('survey'));
        }

        $survey->load('questions');
        /*
        $rules = [
            'responses' => 'required|array|min:1',
            'responses.*' => 'required|string',
        ];
        */

        $rules = [];
        foreach ($survey->questions as $question) {
            $rules["responses.{$question->id}"] = 'required|string|max:1000';
        }

        $request->validate($rules);

        $submittedAt = now();

        $responses = $survey->questions->map(function ($question) use ($request, $survey, $submittedAt) {
            return [
                'survey_id' => $survey->id,
                'question_id' => $question->id,
                'response_value' => $request->input("responses.{$question->id}"),
                'submitted_at' => $submittedAt,
                'created_at' => $submittedAt,
                'updated_at' => $submittedAt,
            ];
        })->toArray();

        SurveyResponse::insert($responses);

        return redirect()->route('surveys.take', $survey)->with('success', 'Thank you! Your survey response has been submitted successfully.');
    }
}
