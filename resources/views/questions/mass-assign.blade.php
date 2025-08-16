@extends('layouts.app')

@section('title', 'Assign Questions to Surveys')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Assign Questions to Surveys</h1>
        <a href="{{ route('questions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Questions
        </a>
    </div>

    @if($questions->count() === 0)
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
            No questions selected. Please go back and select questions to assign.
        </div>
    @else
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <!-- Selected Questions Section -->
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Selected Questions ({{ $questions->count() }})</h2>
            </div>
            <div class="p-6">
                <div class="grid gap-4">
                    @foreach($questions as $question)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900">{{ $question->name }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $question->question_text }}</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-2">
                                        {{ ucfirst(str_replace('-', ' ', $question->question_type)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Survey Selection Section -->
            <div class="bg-gray-50 px-6 py-4 border-t border-b">
                <h2 class="text-lg font-semibold text-gray-900">Select Surveys to Assign To</h2>
                <p class="text-sm text-gray-600 mt-1">Choose which surveys these questions should be added to</p>
            </div>

            <form action="{{ route('questions.mass-assign') }}" method="POST" class="p-6">
                @csrf
                
                <!-- Hidden question IDs -->
                @foreach($questionIds as $questionId)
                    <input type="hidden" name="question_ids[]" value="{{ $questionId }}">
                @endforeach

                @if($surveys->count() > 0)
                    <div class="space-y-4">
                        <div class="flex items-center mb-4">
                            <input type="checkbox" id="select-all-surveys" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <label for="select-all-surveys" class="ml-2 text-sm font-medium text-gray-900">Select All Surveys</label>
                        </div>

                        <div class="grid gap-3">
                            @foreach($surveys as $survey)
                                <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" 
                                           name="survey_ids[]" 
                                           value="{{ $survey->id }}" 
                                           class="survey-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 mt-0.5">
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-900">{{ $survey->name }}</span>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($survey->status === 'created') bg-gray-100 text-gray-800
                                                @elseif($survey->status === 'online') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($survey->status) }}
                                            </span>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500">
                                            Created {{ $survey->created_at->format('M d, Y') }} • 
                                            {{ $survey->questions->count() }} existing questions
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('questions.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Assign Questions to Selected Surveys
                        </button>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">No surveys available to assign questions to.</p>
                        <a href="{{ route('surveys.create') }}" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-block">
                            Create a Survey First
                        </a>
                    </div>
                @endif
            </form>
        </div>
    @endif
</div>

<script>
    const selectAllCheckbox = document.getElementById('select-all-surveys');
    const surveyCheckboxes = document.querySelectorAll('.survey-checkbox');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            surveyCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        surveyCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(surveyCheckboxes).every(cb => cb.checked);
                const noneChecked = Array.from(surveyCheckboxes).every(cb => !cb.checked);
                
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = !allChecked && !noneChecked;
            });
        });
    }
</script>
@endsection