<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Trivia Answer Lookup</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 min-h-screen">
    <!-- Demo Credentials Banner -->
    <div class="bg-yellow-50 border-b-2 border-yellow-200 p-3">
        <div class="max-w-4xl mx-auto text-center">
            <p class="text-sm text-yellow-800">
                <strong>Demo:</strong> Test Code: <code class="bg-yellow-200 px-2 py-1 rounded">1234</code> |
                Admin: <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-800 font-semibold">mrshanebarron@gmail.com</a> / password
            </p>
        </div>
    </div>

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-4xl w-full">
            <!-- Ad Boxes Row -->
            @if($adBoxes->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                @foreach($adBoxes as $ad)
                    <a href="{{ $ad->url }}"
                       target="_blank"
                       onclick="trackAdClick({{ $ad->id }})"
                       class="block hover:scale-105 hover:shadow-xl transition-all duration-300 rounded-xl overflow-hidden">
                        {!! $ad->html_content !!}
                    </a>
                @endforeach
            </div>
            @endif

            <!-- Main Card -->
            <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl p-8 md:p-12 border border-white/20">
                <!-- Icon/Logo Area -->
                <div class="flex justify-center mb-6">
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-4 rounded-2xl shadow-lg">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>

                <h1 class="text-5xl font-bold text-center mb-3 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Trivia Answers
                </h1>
                <p class="text-center text-gray-600 mb-10 text-lg">Enter your 4-digit code to view answers</p>

                <!-- Code Input Form -->
                <form id="lookupForm" class="max-w-md mx-auto">
                    <div class="flex flex-col md:flex-row gap-4">
                        <input
                            type="text"
                            id="codeInput"
                            maxlength="4"
                            placeholder="0000"
                            class="flex-1 px-6 py-4 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 focus:outline-none text-center text-3xl font-bold tracking-widest transition-all duration-200 bg-gray-50/50"
                            required
                        >
                        <button
                            type="submit"
                            class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95"
                        >
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                Unlock Answers
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="resultModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-in fade-in duration-300" onclick="closeModal()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-8 md:p-10 max-h-[85vh] overflow-y-auto transform animate-in zoom-in-95 duration-300" onclick="event.stopPropagation()">
            <div id="modalContent"></div>
            <button
                onclick="closeModal()"
                class="mt-8 w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 font-semibold transition-all duration-300 shadow-lg hover:shadow-xl"
            >
                Close
            </button>
        </div>
    </div>

    <script>
        const form = document.getElementById('lookupForm');
        const modal = document.getElementById('resultModal');
        const modalContent = document.getElementById('modalContent');
        const codeInput = document.getElementById('codeInput');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const code = codeInput.value;

            try {
                const response = await fetch('/api/lookup', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ code })
                });

                const data = await response.json();

                if (data.found) {
                    modalContent.innerHTML = `
                        <div class="mb-8">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full mb-3">
                                        <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm font-semibold text-indigo-700">Answers Unlocked</span>
                                    </div>
                                    <h2 class="text-4xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                                        ${data.title}
                                    </h2>
                                </div>
                                <button
                                    onclick="closeModal()"
                                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-all duration-200"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            ${data.description ? `<p class="text-gray-600 text-lg">${data.description}</p>` : ''}
                        </div>
                        <div class="space-y-3">
                            ${data.answers.map((answer, i) => `
                                <div class="group bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 p-5 rounded-xl border border-indigo-100 hover:border-indigo-200 transition-all duration-200 hover:shadow-md">
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-sm shadow-lg">
                                                ${i + 1}
                                            </span>
                                        </div>
                                        <p class="text-gray-700 leading-relaxed flex-1 pt-1">
                                            ${answer}
                                        </p>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `;
                } else {
                    modalContent.innerHTML = `
                        <div class="text-center">
                            <h2 class="text-2xl font-bold text-red-600 mb-4">Code Not Found</h2>
                            <p class="text-gray-600">The code "${code}" was not found in our database.</p>
                        </div>
                    `;
                }

                modal.classList.remove('hidden');
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        });

        function closeModal() {
            modal.classList.add('hidden');
            codeInput.value = '';
        }

        function trackAdClick(adId) {
            fetch(`/api/ad-click/${adId}`, { method: 'POST' });
        }

        // Close modal on outside click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    </script>
</body>
</html>
