<?php

namespace App\Http\Controllers;

use App\Contracts\QuestionServiceInterface;
use App\Contracts\SurveyServiceInterface;
use App\Http\Requests\BulkDeleteQuestionsRequest;
use App\Http\Requests\MassAssignQuestionsRequest;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Question;
use App\Models\Survey;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function __construct(
        private QuestionServiceInterface $questionService,
        private SurveyServiceInterface $surveyService
    ) {}
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'question_type', 'created_by_id']);
        $questions = $this->questionService->getFilteredQuestions($filters, 15);

        return view('questions.index', compact('questions'));
    }

    public function create()
    {
        return view('questions.create');
    }

    public function store(StoreQuestionRequest $request)
    {
        $this->questionService->createQuestion($request->validated());

        return redirect()->route('questions.index')->with('success', 'Question created successfully.');
    }

    public function show(Question $question)
    {
        $question = $this->questionService->getQuestionWithDetails($question->id);
        $question->load('createdBy', 'surveys');

        return view('questions.show', compact('question'));
    }

    public function edit(Question $question)
    {
        return view('questions.edit', compact('question'));
    }

    public function update(UpdateQuestionRequest $request, Question $question)
    {
        $this->questionService->updateQuestion($question, $request->validated());

        return redirect()->route('questions.index')->with('success', 'Question updated successfully.');
    }

    public function destroy(Question $question)
    {
        //$this->questionService->deleteQuestion($question);

        return redirect()->route('questions.index')->with('success', 'Question deleted successfully.');
    }

    public function showMassAssign(Request $request)
    {
        $questionIds = $request->input('question_ids', []);

        // Get selected questions
        $questions = Question::whereIn('id', $questionIds)->get();

        // Use SurveyService for filtered surveys
        $surveyFilters = $request->only(['survey_name', 'survey_status']);

        // Map filter names to match SurveyRepository expectations
        $mappedFilters = [];
        if (!empty($surveyFilters['survey_name'])) {
            $mappedFilters['name'] = $surveyFilters['survey_name'];
        }
        if (!empty($surveyFilters['survey_status'])) {
            $mappedFilters['status'] = $surveyFilters['survey_status'];
        }

        $surveys = $this->surveyService->getFilteredSurveys($mappedFilters, 10);

        return view('questions.mass-assign', compact('questions', 'surveys', 'questionIds'));
    }

    public function massAssign(MassAssignQuestionsRequest $request)
    {
        $validated = $request->validated();
        $createdById = auth()->id() ?? 1;

        // Get questions that already exist in the selected surveys
        $existingQuestions = $this->questionService->getExistingQuestionsInSurveys(
            $validated['question_ids'],
            $validated['survey_ids']
        );

        $success = $this->questionService->assignQuestionsToSurveys(
            $validated['question_ids'],
            $validated['survey_ids'],
            $createdById
        );

        if (!$success) {
            return redirect()->route('questions.index')
                ->withErrors(['error' => 'Failed to assign questions to surveys. Please try again.']);
        }



        return redirect()->route('questions.index')
            ->with('success', "Questions assigned to surveys successfully.")
            ->with('info', $existingQuestions);
    }

    public function massDelete(BulkDeleteQuestionsRequest $request)
    {
        $validated = $request->validated();
        $questionIds = $validated['question_ids'];
        $totalQuestions = count($questionIds);

        // Get questions that are linked to surveys (won't be deleted)
        $linkedQuestions = $this->questionService->getLinkedQuestions($questionIds);

        // Attempt to delete questions (service will automatically exclude linked ones)
        $success = $this->questionService->bulkDeleteQuestions($questionIds);

        if (!$success) {
            return redirect()->route('questions.index')
                ->withErrors(['error' => 'Failed to delete questions. Please try again.']);
        }

        // Calculate actual deletions
        $deletedCount = $totalQuestions - count($linkedQuestions);
        $message = "From {$totalQuestions} question(s) to be deleted, {$deletedCount} were successfully deleted.";

        return redirect()->route('questions.index')
            ->with('success', $message)
            ->with('warnings', $linkedQuestions);
    }
}
