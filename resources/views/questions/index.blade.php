@extends('layouts.app')

@section('title', 'Questions')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Questions</h1>
    <a href="{{ route('questions.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Create New Question
    </a>
</div>

<!-- Filters -->
<div class="bg-white shadow rounded-lg p-6 mb-6">
    <form method="GET" action="{{ route('questions.index') }}" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-0">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Question Name</label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ request('name') }}" 
                   placeholder="Search by question name..."
                   class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div class="min-w-0">
            <label for="question_type" class="block text-sm font-medium text-gray-700 mb-1">Question Type</label>
            <select id="question_type" 
                    name="question_type" 
                    class="border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Types</option>
                <option value="rating" {{ request('question_type') === 'rating' ? 'selected' : '' }}>Rating</option>
                <option value="comment-only" {{ request('question_type') === 'comment-only' ? 'selected' : '' }}>Comment Only</option>
                <option value="multiple-choice" {{ request('question_type') === 'multiple-choice' ? 'selected' : '' }}>Multiple Choice</option>
            </select>
        </div>
        
        <div class="flex space-x-2">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Filter
            </button>
            <a href="{{ route('questions.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Clear
            </a>
        </div>
    </form>
</div>

@if($questions->count() > 0)
    <form id="mass-action-form" method="POST" class="mb-4">
        @csrf
        <div class="bg-white shadow rounded-lg p-4 mb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="select-all" class="form-checkbox">
                        <span class="ml-2">Select All</span>
                    </label>
                    <span class="text-sm text-gray-500" id="selected-count">0 selected</span>
                </div>
                <div class="flex space-x-2">
                    <button type="button" onclick="massAssign()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50" id="assign-btn" disabled>
                        Assign to Surveys
                    </button>
                    <button type="button" onclick="massDelete()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50" id="delete-btn" disabled>
                        Delete Selected
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @foreach($questions as $question)
                    <li>
                        <div class="px-4 py-4 flex items-center">
                            <input type="checkbox" name="question_ids[]" value="{{ $question->id }}" class="question-checkbox mr-4">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-indigo-600 truncate">
                                        <a href="{{ route('questions.show', $question) }}">{{ $question->name }}</a>
                                    </p>
                                    <div class="ml-2 flex-shrink-0">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst(str_replace('-', ' ', $question->question_type)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600">{{ Str::limit($question->question_text, 100) }}</p>
                                </div>
                                <div class="mt-2 flex justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            Created by {{ $question->createdBy->name ?? 'Unknown' }}
                                        </p>
                                        <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                            {{ $question->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('questions.edit', $question) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form action="{{ route('questions.destroy', $question) }}" method="POST" class="inline">
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
    </form>
    
    <!-- Pagination -->
    <div class="mt-6">
        {{ $questions->links() }}
    </div>

    <script>
        const selectAllCheckbox = document.getElementById('select-all');
        const questionCheckboxes = document.querySelectorAll('.question-checkbox');
        const selectedCount = document.getElementById('selected-count');
        const assignBtn = document.getElementById('assign-btn');
        const deleteBtn = document.getElementById('delete-btn');

        function updateUI() {
            const checkedBoxes = document.querySelectorAll('.question-checkbox:checked');
            selectedCount.textContent = `${checkedBoxes.length} selected`;
            assignBtn.disabled = checkedBoxes.length === 0;
            deleteBtn.disabled = checkedBoxes.length === 0;
        }

        selectAllCheckbox.addEventListener('change', function() {
            questionCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateUI();
        });

        questionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateUI);
        });

        function massAssign() {
            const checkedBoxes = document.querySelectorAll('.question-checkbox:checked');
            if (checkedBoxes.length === 0) return;
            
            const form = document.getElementById('mass-action-form');
            form.action = '{{ route("questions.mass-assign.form") }}';
            form.method = 'GET';
            form.submit();
        }

        function massDelete() {
            const checkedBoxes = document.querySelectorAll('.question-checkbox:checked');
            if (checkedBoxes.length === 0) return;
            
            if (confirm(`Are you sure you want to delete ${checkedBoxes.length} questions?`)) {
                const form = document.getElementById('mass-action-form');
                form.action = '{{ route("questions.mass-delete") }}';
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                form.submit();
            }
        }
    </script>
@else
    <div class="text-center py-12">
        <p class="text-gray-500 text-xl">No questions found.</p>
        <a href="{{ route('questions.create') }}" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-block">
            Create Your First Question
        </a>
    </div>
@endif
@endsection