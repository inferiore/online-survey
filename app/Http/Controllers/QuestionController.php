<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Survey;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with('createdBy');

        // Filter by question name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by question type
        if ($request->filled('question_type')) {
            $query->where('question_type', $request->question_type);
        }

        $questions = $query->latest()->paginate(15)->withQueryString();

        return view('questions.index', compact('questions'));
    }

    public function create()
    {
        return view('questions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'question_text' => 'required|string',
            'question_type' => 'required|in:rating,comment-only,multiple-choice',
        ]);

        Question::create([
            'name' => $request->name,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'created_by_id' => auth()->id()??1,
        ]);

        return redirect()->route('questions.index')->with('success', 'Question created successfully.');
    }

    public function show(Question $question)
    {
        $question->load('createdBy', 'surveys');
        return view('questions.show', compact('question'));
    }

    public function edit(Question $question)
    {
        return view('questions.edit', compact('question'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'question_text' => 'required|string',
            'question_type' => 'required|in:rating,comment-only,multiple-choice',
        ]);

        $question->update([
            'name' => $request->name,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
        ]);

        return redirect()->route('questions.index')->with('success', 'Question updated successfully.');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('questions.index')->with('success', 'Question deleted successfully.');
    }

    public function showMassAssign(Request $request)
    {
        $questionIds = $request->input('question_ids', []);
        $questions = Question::whereIn('id', $questionIds)->get();
        
        // Build surveys query with filters
        $surveysQuery = Survey::query();
        
        // Filter by survey name
        if ($request->filled('survey_name')) {
            $surveysQuery->where('name', 'like', '%' . $request->survey_name . '%');
        }
        
        // Filter by survey status
        if ($request->filled('survey_status')) {
            $surveysQuery->where('status', $request->survey_status);
        }
        
        $surveys = $surveysQuery->latest()->paginate(10)->withQueryString();

        return view('questions.mass-assign', compact('questions', 'surveys', 'questionIds'));
    }

    public function massAssign(Request $request)
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
            'survey_ids' => 'required|array',
            'survey_ids.*' => 'exists:surveys,id',
        ]);

        foreach ($request->survey_ids as $surveyId) {
            $survey = Survey::find($surveyId);
            foreach ($request->question_ids as $questionId) {
                $survey->questions()->syncWithoutDetaching([$questionId => ['created_by_id' => auth()->id()??1]]);
            }
        }

        return redirect()->route('questions.index')->with('success', 'Questions assigned to surveys successfully.');
    }

    public function massDelete(Request $request)
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        Question::whereIn('id', $request->question_ids)->delete();

        return redirect()->route('questions.index')->with('success', 'Questions deleted successfully.');
    }
}
