@extends('layouts.app')

@section('title', 'Surveys')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Surveys</h1>
    <a href="{{ route('surveys.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Create New Survey
    </a>
</div>

<!-- Filters -->
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <form method="GET" action="{{ route('surveys.index') }}" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-0">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Survey Name</label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ request('name') }}" 
                   placeholder="Search by survey name..."
                   class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div class="min-w-0">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="status" 
                    name="status" 
                    class="border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Statuses</option>
                <option value="created" {{ request('status') === 'created' ? 'selected' : '' }}>Created</option>
                <option value="online" {{ request('status') === 'online' ? 'selected' : '' }}>Online</option>
                <option value="finished" {{ request('status') === 'finished' ? 'selected' : '' }}>Finished</option>
            </select>
        </div>
        
        <div class="flex space-x-2">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Filter
            </button>
            <a href="{{ route('surveys.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Clear
            </a>
        </div>
    </form>
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
    
    <!-- Pagination -->
    <div class="mt-6">
        {{ $surveys->links() }}
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