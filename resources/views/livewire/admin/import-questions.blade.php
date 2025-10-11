<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">
                            Import Trivia Questions
                        </h2>
                        <p class="mt-2 text-sm text-gray-600">
                            Automatically import questions from The Trivia API and schedule them for your contest.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 bg-opacity-25 p-6 sm:px-20">
                @if (session()->has('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <form wire:submit="import" class="space-y-6">
                    <!-- Number of Questions -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">
                            Number of Questions
                        </label>
                        <input
                            type="number"
                            wire:model="amount"
                            id="amount"
                            min="1"
                            max="50"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                        @error('amount') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="mt-1 text-sm text-gray-500">Maximum 50 questions per import</p>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">
                            Category (Optional)
                        </label>
                        <select
                            wire:model="category"
                            id="category"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="">All Categories</option>
                            @foreach ($categories as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Difficulty -->
                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-700">
                            Difficulty (Optional)
                        </label>
                        <select
                            wire:model="difficulty"
                            id="difficulty"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="">All Difficulties</option>
                            @foreach ($difficulties as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('difficulty') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label for="startDate" class="block text-sm font-medium text-gray-700">
                            Start Date
                        </label>
                        <input
                            type="date"
                            wire:model="startDate"
                            id="startDate"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                        @error('startDate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        <p class="mt-1 text-sm text-gray-500">Questions will be scheduled starting from this date, one per day at random times</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-50 disabled:cursor-not-allowed transition ease-in-out duration-150"
                        >
                            <span wire:loading.remove>Import Questions</span>
                            <span wire:loading>
                                <svg class="animate-spin h-4 w-4 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Importing...
                            </span>
                        </button>

                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            Back to Dashboard
                        </a>
                    </div>
                </form>

                <!-- Result Display -->
                @if ($result)
                    <div class="mt-6 p-4 @if($result['success']) bg-green-50 border-green-200 @else bg-red-50 border-red-200 @endif border rounded-md">
                        <h3 class="font-semibold @if($result['success']) text-green-800 @else text-red-800 @endif">
                            Import Results
                        </h3>
                        <p class="mt-2 @if($result['success']) text-green-700 @else text-red-700 @endif">
                            {{ $result['message'] }}
                        </p>
                        <p class="mt-1 text-sm @if($result['success']) text-green-600 @else text-red-600 @endif">
                            Questions imported: {{ $result['imported'] }}
                        </p>
                        @if (!empty($result['errors']))
                            <div class="mt-3">
                                <p class="text-sm font-medium text-red-700">Errors:</p>
                                <ul class="mt-1 text-sm text-red-600 list-disc list-inside">
                                    @foreach ($result['errors'] as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Info Box -->
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                    <h3 class="font-semibold text-blue-800">How It Works</h3>
                    <ul class="mt-2 text-sm text-blue-700 space-y-1">
                        <li>• Questions are fetched from The Trivia API (10,000+ curated questions)</li>
                        <li>• Each question is automatically scheduled at a random time (8 AM - 8 PM)</li>
                        <li>• Questions start from your chosen date, one per day</li>
                        <li>• All questions are set to inactive initially - activate them when ready</li>
                        <li>• You can review and edit any imported question before it goes live</li>
                        <li>• Default prize amount: $10.00 per question (editable)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
