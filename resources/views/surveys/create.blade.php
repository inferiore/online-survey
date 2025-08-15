@extends('layouts.app')

@section('title', 'Create Survey')

@section('content')
<div class="max-w-md mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Create New Survey</h1>

    <form action="{{ route('surveys.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                Survey Name
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" 
                   id="name" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   placeholder="Enter survey name">
            @error('name')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                Status
            </label>
            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('status') border-red-500 @enderror" 
                    id="status" 
                    name="status">
                <option value="created" {{ old('status') == 'created' ? 'selected' : '' }}>Created</option>
                <option value="online" {{ old('status') == 'online' ? 'selected' : '' }}>Online</option>
                <option value="finished" {{ old('status') == 'finished' ? 'selected' : '' }}>Finished</option>
            </select>
            @error('status')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                Create Survey
            </button>
            <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="{{ route('surveys.index') }}">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection