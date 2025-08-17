<?php

namespace App\Http\Controllers;

use App\Contracts\SurveyResponseServiceInterface;
use App\Contracts\SurveyServiceInterface;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\SubmitSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Models\Survey;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function __construct(
        private SurveyServiceInterface $surveyService,
        private SurveyResponseServiceInterface $responseService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'status']);
        $surveys = $this->surveyService->getFilteredSurveys($filters);

        return view('surveys.index', compact('surveys'));
    }

    public function create()
    {
        return view('surveys.create');
    }

    public function store(StoreSurveyRequest $request)
    {
        $this->surveyService->createSurvey($request->validated());

        return redirect()->route('surveys.index')->with('success', 'Survey created successfully.');
    }

    public function show(Survey $survey)
    {
        $survey = $this->surveyService->getSurveyWithDetails($survey->id);

        return view('surveys.show', compact('survey'));
    }

    public function edit(Survey $survey)
    {
        return view('surveys.edit', compact('survey'));
    }

    public function update(UpdateSurveyRequest $request, Survey $survey)
    {
        $this->surveyService->updateSurvey($survey, $request->validated());

        return redirect()->route('surveys.index')->with('success', 'Survey updated successfully.');
    }

    public function destroy(Survey $survey)
    {
        $this->surveyService->deleteSurvey($survey);

        return redirect()->route('surveys.index')->with('success', 'Survey deleted successfully.');
    }

    public function takeSurvey(Survey $survey)
    {
        $surveyForTaking = $this->surveyService->getSurveyForTaking($survey);

        if (!$surveyForTaking) {
            return view('surveys.not-available', compact('survey'));
        }

        return view('surveys.take', ['survey' => $surveyForTaking]);
    }

    public function submitSurvey(SubmitSurveyRequest $request, Survey $survey)
    {
        if (!$this->responseService->validateSurveyAvailability($survey)) {
            return view('surveys.not-available', compact('survey'));
        }

        $success = $this->responseService->submitResponses($survey, $request->validated());

        if (!$success) {
            return redirect()->route('surveys.take', $survey)
                ->withErrors(['error' => 'Failed to submit survey responses. Please try again.']);
        }

        return redirect()->route('surveys.take', $survey)
            ->with('success', 'Thank you! Your survey response has been submitted successfully.');
    }
}
