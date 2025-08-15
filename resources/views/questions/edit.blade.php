@extends('layouts.app')

@section('title', 'Edit Question')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Question</h1>

    <form action="{{ route('questions.update', $question) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                Question Name
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" 
                   id="name" 
                   type="text" 
                   name="name" 
                   value="{{ old('name', $question->name) }}" 
                   placeholder="Enter question name">
            @error('name')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="question_text">
                Question Text
            </label>
            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('question_text') border-red-500 @enderror" 
                      id="question_text" 
                      name="question_text" 
                      rows="4" 
                      placeholder="Enter the full question text">{{ old('question_text', $question->question_text) }}</textarea>
            @error('question_text')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="question_type">
                Question Type
            </label>
            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('question_type') border-red-500 @enderror" 
                    id="question_type" 
                    name="question_type">
                <option value="">Select question type</option>
                <option value="rating" {{ old('question_type', $question->question_type) == 'rating' ? 'selected' : '' }}>Rating</option>
                <option value="comment-only" {{ old('question_type', $question->question_type) == 'comment-only' ? 'selected' : '' }}>Comment Only</option>
                <option value="multiple-choice" {{ old('question_type', $question->question_type) == 'multiple-choice' ? 'selected' : '' }}>Multiple Choice</option>
            </select>
            @error('question_type')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                Update Question
            </button>
            <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="{{ route('questions.index') }}">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection