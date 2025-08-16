<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Not Available</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-8">
        <div class="max-w-md mx-auto px-4">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="bg-red-600 text-white px-6 py-4">
                    <div class="flex items-center">
                        <div class="text-red-200 text-2xl mr-3">⚠️</div>
                        <h1 class="text-xl font-bold">Survey Not Available</h1>
                    </div>
                </div>

                <div class="p-6 text-center">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $survey->name }}</h2>
                        <div class="text-gray-600 mb-4">
                            Sorry, this survey is currently not available for responses.
                        </div>
                    </div>

                    <div class="mb-6">
                        @if($survey->status === 'created')
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="text-yellow-600 text-lg mr-2">🚧</div>
                                    <div class="text-left">
                                        <div class="font-medium text-yellow-800">Survey in Development</div>
                                        <div class="text-sm text-yellow-700">This survey is still being prepared and is not yet ready for responses.</div>
                                    </div>
                                </div>
                            </div>
                        @elseif($survey->status === 'finished')
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="text-gray-600 text-lg mr-2">✅</div>
                                    <div class="text-left">
                                        <div class="font-medium text-gray-800">Survey Completed</div>
                                        <div class="text-sm text-gray-700">This survey has been closed and is no longer accepting responses.</div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="text-red-600 text-lg mr-2">❌</div>
                                    <div class="text-left">
                                        <div class="font-medium text-red-800">Survey Unavailable</div>
                                        <div class="text-sm text-red-700">This survey is currently not accepting responses.</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-3">
                        @if($survey->status === 'created')
                            <p class="text-sm text-gray-600">
                                Please check back later when the survey becomes available.
                            </p>
                        @elseif($survey->status === 'finished')
                            <p class="text-sm text-gray-600">
                                Thank you to everyone who participated in this survey.
                            </p>
                        @endif

                        <div class="pt-4">
                            <button onclick="window.history.back()" 
                                    class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                Go Back
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 text-center">
                    <p class="text-xs text-gray-500">
                        Survey created {{ $survey->created_at->format('M d, Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>