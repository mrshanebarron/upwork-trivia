<script setup>
import { Link } from '@inertiajs/vue3';
import SkyGrassBackground from '@/Components/Animations/SkyGrassBackground.vue';
import CloudsAnimation from '@/Components/Animations/CloudsAnimation.vue';
</script>

<template>
    <div class="animated-page">
        <SkyGrassBackground />
        <CloudsAnimation />

        <!-- Header with Logo and Menu -->
        <div class="fixed top-0 left-0 right-0 z-50 py-4 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <!-- Logo -->
                <Link :href="route('home')" class="cursor-pointer">
                    <img src="/images/logo.webp" alt="Poop Bag Trivia" class="h-20 sm:h-24 w-auto">
                </Link>

                <!-- Navigation Menu -->
                <nav class="flex gap-4 sm:gap-6 items-center">
                    <Link :href="route('home')" class="text-white font-bold text-sm sm:text-base hover:text-yellow-300 transition-colors">
                        Home
                    </Link>
                    <Link :href="route('dashboard')" class="text-white font-bold text-sm sm:text-base hover:text-yellow-300 transition-colors">
                        Dashboard
                    </Link>
                    <Link :href="route('dashboard.winnings')" class="text-white font-bold text-sm sm:text-base hover:text-yellow-300 transition-colors">
                        Winnings
                    </Link>
                    <Link :href="route('dashboard.gift-cards')" class="text-white font-bold text-sm sm:text-base hover:text-yellow-300 transition-colors">
                        Gift Cards
                    </Link>

                    <!-- User Menu -->
                    <div class="flex items-center gap-3 ml-4 pl-4 border-l-2 border-white/30">
                        <span class="text-white font-semibold text-sm hidden sm:block">
                            {{ $page.props.auth?.user?.name }}
                        </span>
                        <Link :href="route('logout')" method="post" as="button" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-bold text-sm rounded-lg transition-colors">
                            Logout
                        </Link>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="pt-32 pb-12 relative z-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <slot />
            </div>
        </div>
    </div>
</template>
