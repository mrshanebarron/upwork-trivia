<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Trivia Answers' }}</title>

        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#5b21b6">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Trivia">
        <link rel="manifest" href="/manifest.json">
        <link rel="apple-touch-icon" href="/images/icon-192.png">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 min-h-screen relative overflow-hidden">
        <!-- Animated background elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-400/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
        </div>

        <div class="relative z-10">
            {{ $slot }}
        </div>

        @livewireScripts

        <!-- PWA Service Worker Registration -->
        <script>
            // Service Worker Registration
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(registration => console.log('SW registered:', registration))
                        .catch(err => console.log('SW registration failed:', err));
                });
            }

            // PWA Install Prompt
            let deferredPrompt;
            const installBanner = document.getElementById('installBanner');
            const installBtn = document.getElementById('installBtn');
            const dismissBtn = document.getElementById('dismissBtn');

            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                if (installBanner) {
                    installBanner.classList.remove('hidden');
                }
            });

            if (installBtn) {
                installBtn.addEventListener('click', async () => {
                    if (!deferredPrompt) return;
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    deferredPrompt = null;
                    if (installBanner) {
                        installBanner.classList.add('hidden');
                    }
                });
            }

            if (dismissBtn) {
                dismissBtn.addEventListener('click', () => {
                    if (installBanner) {
                        installBanner.classList.add('hidden');
                    }
                });
            }
        </script>
    </body>
</html>
