<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Trivia Code
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.trivia-codes.update', $triviaCode) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Code (4 digits)</label>
                            <input type="text" name="code" maxlength="4" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('code') border-red-500 @enderror" value="{{ old('code', $triviaCode->code) }}">
                            @error('code')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                            <input type="text" name="title" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('title') border-red-500 @enderror" value="{{ old('title', $triviaCode->title) }}">
                            @error('title')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                            <textarea name="description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('description', $triviaCode->description) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="active" value="1" {{ old('active', $triviaCode->is_active) ? 'checked' : '' }} class="rounded">
                                <span class="ml-2 text-gray-700">Active</span>
                            </label>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Answers</label>
                            <div id="answers">
                                @foreach($triviaCode->answers as $answer)
                                <div class="answer-row flex mb-2">
                                    <input type="text" name="answers[]" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ $answer->answer }}">
                                    <button type="button" onclick="removeAnswer(this)" class="ml-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Remove</button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" onclick="addAnswer()" class="mt-2 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Add Answer</button>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-grass hover:bg-grass-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Update
                            </button>
                            <a href="{{ route('admin.trivia-codes.index') }}" class="text-gray-600 hover:text-gray-800">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addAnswer() {
            const div = document.createElement('div');
            div.className = 'answer-row flex mb-2';
            div.innerHTML = `
                <input type="text" name="answers[]" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <button type="button" onclick="removeAnswer(this)" class="ml-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Remove</button>
            `;
            document.getElementById('answers').appendChild(div);
        }

        function removeAnswer(btn) {
            if (document.querySelectorAll('.answer-row').length > 1) {
                btn.parentElement.remove();
            }
        }
    </script>
</x-app-layout>
