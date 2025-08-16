<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $survey->name }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="bg-blue-600 text-white px-6 py-4">
                    <h1 class="text-2xl font-bold">{{ $survey->name }}</h1>
                    <p class="text-blue-100 mt-1">Please take a few minutes to complete this survey</p>
                </div>

                @if(session('success'))
                    <div class="p-6 text-center">
                        <div class="text-green-600 text-6xl mb-4">✓</div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Survey Submitted!</h2>
                        <p class="text-gray-600">Thank you for your valuable feedback.</p>
                    </div>
                @else
                    <form action="{{ route('surveys.submit', $survey) }}" method="POST" class="p-6">
                        @csrf

                        @foreach($survey->questions as $index => $question)
                            <div class="mb-8 @if($index > 0) border-t pt-6 @endif">
                                <div class="mb-4">
                                    <label class="block text-lg font-medium text-gray-900 mb-2">
                                        {{ $index + 1 }}. {{ $question->question_text }}
                                        <span class="text-red-500">*</span>
                                    </label>

                                    @if($question->question_type === 'rating')
                                        <div class="flex space-x-4">
                                            @for($i = 1; $i <= 5; $i++)
                                                <label class="flex flex-col items-center cursor-pointer">
                                                    <input type="radio"
                                                           name="responses[{{ $question->id }}]"
                                                           value="{{ $i }}"
                                                           class="sr-only peer"
                                                           {{ old("responses.{$question->id}") == $i ? 'checked' : '' }}>
                                                    <div class="w-12 h-12 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-blue-500 peer-checked:bg-blue-500 peer-checked:text-white transition-colors">
                                                        {{ $i }}
                                                    </div>
                                                    <span class="text-xs text-gray-500 mt-1">
                                                        @if($i == 1) Poor
                                                        @elseif($i == 2) Fair
                                                        @elseif($i == 3) Good
                                                        @elseif($i == 4) Very Good
                                                        @else Excellent
                                                        @endif
                                                    </span>
                                                </label>
                                            @endfor
                                        </div>

                                    @elseif($question->question_type === 'multiple-choice')
                                        <div class="space-y-2">
                                            @php
                                                $options = ['Very Likely', 'Likely', 'Neutral', 'Unlikely', 'Very Unlikely'];
                                                if(str_contains(strtolower($question->question_text), 'feature')) {
                                                    $options = ['Ease of Use', 'Performance', 'Design', 'Customer Support', 'Pricing'];
                                                } elseif(str_contains(strtolower($question->question_text), 'hear about')) {
                                                    $options = ['Search Engine', 'Social Media', 'Word of Mouth', 'Advertisement', 'Other'];
                                                } elseif(str_contains(strtolower($question->question_text), 'contact')) {
                                                    $options = ['Email', 'Phone', 'Live Chat', 'In Person', 'Text Message'];
                                                }
                                            @endphp

                                            @foreach($options as $option)
                                                <label class="flex items-center">
                                                    <input type="radio"
                                                           name="responses[{{ $question->id }}]"
                                                           value="{{ $option }}"
                                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"
                                                           {{ old("responses.{$question->id}") == $option ? 'checked' : '' }}>
                                                    <span class="ml-2 text-gray-700">{{ $option }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                    @else
                                        <textarea name="responses[{{ $question->id }}]"
                                                  rows="4"
                                                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error("responses.{$question->id}") border-red-500 @enderror"
                                                  placeholder="Enter your response...">{{ old("responses.{$question->id}") }}</textarea>
                                    @endif

                                    @error("responses.{$question->id}")
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endforeach

                        <div class="border-t pt-6">
                            <button type="submit"
                                    class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                Submit Survey
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            @unless(session('success'))
                <div class="text-center mt-6 text-sm text-gray-500">
                    All fields marked with <span class="text-red-500">*</span> are required
                </div>
            @endunless
        </div>
    </div>
</body>
</html>
