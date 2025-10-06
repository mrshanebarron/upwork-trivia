<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Trivia Answer Lookup</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-bone min-h-screen">
    <!-- Demo Credentials Banner -->
    <div class="bg-yellow-50 border-b-2 border-yellow-200 p-3">
        <div class="max-w-4xl mx-auto text-center">
            <p class="text-sm text-yellow-800">
                <strong>Demo:</strong> Test Code: <code class="bg-yellow-200 px-2 py-1 rounded">1234</code>
            </p>
        </div>
    </div>

    <div x-data="{
        code: '',
        showModal: false,
        result: null,
        error: '',
        async lookup() {
            this.error = '';
            try {
                const response = await fetch('/api/lookup', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ code: this.code })
                });
                const data = await response.json();
                if (data.found) {
                    this.result = data;
                    this.showModal = true;
                } else {
                    this.error = 'Code not found';
                }
            } catch (e) {
                this.error = 'An error occurred. Please try again.';
            }
        },
        closeModal() {
            this.showModal = false;
            this.code = '';
            this.result = null;
        },
        trackAdClick(adId) {
            fetch('/api/ad-click/' + adId, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                }
            });
        }
    }" class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-4xl w-full">
            <!-- Ad Boxes Row -->
            @if($adBoxes->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                @foreach($adBoxes as $index => $ad)
                    @if($ad->html_content)
                        <a href="{{ $ad->url }}"
                           target="_blank"
                           @click.prevent="trackAdClick({{ $ad->id }}); window.open('{{ $ad->url }}', '_blank')"
                           class="block hover:scale-105 hover:shadow-xl transition-all duration-300 rounded-xl overflow-hidden">
                            {!! $ad->html_content !!}
                        </a>
                    @else
                        @php
                            $colors = ['bg-forest', 'bg-grass', 'bg-sky'];
                            $bgColor = $colors[$index % count($colors)];
                        @endphp
                        <a href="{{ $ad->url }}"
                           target="_blank"
                           @click.prevent="trackAdClick({{ $ad->id }}); window.open('{{ $ad->url }}', '_blank')"
                           class="block {{ $bgColor }} text-white p-6 rounded-xl shadow-lg hover:scale-105 hover:shadow-xl transition-all duration-300">
                            <h3 class="font-bold text-xl mb-2">{{ $ad->title }}</h3>
                            <p>{{ $ad->description ?? 'Click to learn more!' }}</p>
                        </a>
                    @endif
                @endforeach
            </div>
            @endif

            <!-- Main Card -->
            <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl p-8 md:p-12 border border-white/20">
                <!-- Icon/Logo Area -->
                <div class="flex justify-center mb-6">
                    <div class="bg-gradient-to-br from-forest to-grass p-4 rounded-2xl shadow-lg">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>

                <h1 class="text-5xl font-bold text-center mb-3 bg-gradient-to-r from-forest via-grass to-sky bg-clip-text text-transparent">
                    Trivia Answers
                </h1>
                <p class="text-center text-gray-600 mb-10 text-lg">Enter your 4-digit code to view answers</p>

                <!-- Code Input Form -->
                <form @submit.prevent="lookup" class="max-w-md mx-auto">
                    <div class="flex flex-col md:flex-row gap-4">
                        <input
                            type="text"
                            x-model="code"
                            maxlength="4"
                            placeholder="0000"
                            required
                            class="flex-1 px-6 py-4 border-2 border-gray-200 rounded-xl focus:border-grass focus:ring-4 focus:ring-grass/20 focus:outline-none text-center text-3xl font-bold tracking-widest transition-all duration-200 bg-bone/80"
                        >
                        <button
                            type="submit"
                            class="px-8 py-4 bg-gradient-to-r from-forest to-grass text-white rounded-xl hover:from-forest-700 hover:to-grass-700 font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95"
                        >
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                Unlock Answers
                            </span>
                        </button>
                    </div>

                    <div x-show="error" x-text="error" class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg text-red-700 font-medium"></div>
                </form>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="showModal"
             @click="closeModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50"
             style="display: none;">
            <div @click.stop
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-8 md:p-10 max-h-[85vh] overflow-y-auto">
                <template x-if="result">
                    <div>
                        <div class="mb-8">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-gradient-to-r from-forest-100 to-grass-100 rounded-full mb-3">
                                        <svg class="w-4 h-4 text-forest-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm font-semibold text-forest-700">Answers Unlocked</span>
                                    </div>
                                    <h2 class="text-4xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent" x-text="result.title"></h2>
                                </div>
                                <button @click="closeModal" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-all duration-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <p x-show="result.description" x-text="result.description" class="text-gray-600 text-lg"></p>
                        </div>
                        <div class="space-y-3">
                            <template x-for="(answer, index) in result.answers" :key="index">
                                <div class="group bg-gradient-to-br from-forest-50 via-grass-50 to-sky-50 p-5 rounded-xl border border-forest-100 hover:border-forest-200 transition-all duration-200 hover:shadow-md">
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-forest to-grass text-white font-bold text-sm shadow-lg" x-text="index + 1"></span>
                                        </div>
                                        <p class="text-gray-700 leading-relaxed flex-1 pt-1" x-text="answer"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <button @click="closeModal" class="mt-8 w-full py-4 bg-gradient-to-r from-forest to-grass text-white rounded-xl hover:from-forest-700 hover:to-grass-700 font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
                            Close
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</body>
</html>
