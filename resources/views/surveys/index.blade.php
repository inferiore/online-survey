@extends('layouts.app')

@section('title', 'Surveys')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Surveys</h1>
    <a href="{{ route('surveys.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Create New Survey
    </a>
</div>

@if($surveys->count() > 0)
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @foreach($surveys as $survey)
                <li>
                    <div class="px-4 py-4 flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-indigo-600 truncate">
                                    <a href="{{ route('surveys.show', $survey) }}">{{ $survey->name }}</a>
                                </p>
                                <div class="ml-2 flex-shrink-0">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($survey->status === 'created') bg-gray-100 text-gray-800
                                        @elseif($survey->status === 'online') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($survey->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2 flex justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500">
                                        Created by {{ $survey->createdBy->name ?? 'Unknown' }}
                                    </p>
                                    <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                        {{ $survey->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('surveys.edit', $survey) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    <form action="{{ route('surveys.destroy', $survey) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@else
    <div class="text-center py-12">
        <p class="text-gray-500 text-xl">No surveys found.</p>
        <a href="{{ route('surveys.create') }}" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-block">
            Create Your First Survey
        </a>
    </div>
@endif
@endsection