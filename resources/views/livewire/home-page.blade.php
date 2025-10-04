<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-4xl w-full">
        <!-- Ad Boxes Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            @foreach($adBoxes as $ad)
                <a href="{{ $ad->url }}"
                   target="_blank"
                   wire:click="trackAdClick({{ $ad->id }})"
                   class="block hover:opacity-80 transition">
                    {!! $ad->html_content !!}
                </a>
            @endforeach
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <h1 class="text-4xl font-bold text-center mb-2 text-gray-800">Trivia Answers</h1>
            <p class="text-center text-gray-600 mb-8">Enter your 4-digit code to view answers</p>

            <!-- Code Input Form -->
            <form wire:submit="submit" class="max-w-md mx-auto">
                <div class="flex gap-4">
                    <input
                        type="text"
                        wire:model="code"
                        maxlength="4"
                        placeholder="Enter code..."
                        class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-500 focus:outline-none text-center text-2xl font-bold tracking-widest"
                    >
                    <button
                        type="submit"
                        class="px-8 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-semibold transition"
                    >
                        View Answers
                    </button>
                </div>

                @if($error)
                    <p class="text-red-500 text-center mt-4 font-semibold">{{ $error }}</p>
                @endif
            </form>
        </div>
    </div>

    <!-- Modal -->
    @if($showModal && $triviaCode)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
             wire:click="closeModal">
            <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full p-8 max-h-[80vh] overflow-y-auto"
                 wire:click.stop>
                <div class="flex justify-between items-start mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">{{ $triviaCode->title }}</h2>
                    <button
                        wire:click="closeModal"
                        class="text-gray-500 hover:text-gray-700 text-3xl font-bold"
                    >
                        ×
                    </button>
                </div>

                @if($triviaCode->description)
                    <p class="text-gray-600 mb-6">{{ $triviaCode->description }}</p>
                @endif

                <div class="space-y-4">
                    @foreach($triviaCode->answers as $answer)
                        <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                            <p class="text-gray-800 leading-relaxed">
                                <span class="font-bold text-purple-600">{{ $answer->order }}.</span>
                                {{ $answer->answer }}
                            </p>
                        </div>
                    @endforeach
                </div>

                <button
                    wire:click="closeModal"
                    class="mt-6 w-full py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-semibold transition"
                >
                    Close
                </button>
            </div>
        </div>
    @endif
</div>
