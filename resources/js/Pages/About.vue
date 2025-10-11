<script setup>
import { Head, Link } from '@inertiajs/vue3';
import SkyGrassBackground from '@/Components/Animations/SkyGrassBackground.vue';
import CloudsAnimation from '@/Components/Animations/CloudsAnimation.vue';
import PuppyAnimation from '@/Components/Animations/PuppyAnimation.vue';
import CartoonCard from '@/Components/Animations/CartoonCard.vue';

defineProps({
    content: String,
});
</script>

<template>
    <Head title="About" />

    <div class="animated-page">
        <!-- Animated Background -->
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
                    <Link :href="route('about')" class="text-yellow-300 font-bold text-sm sm:text-base">
                        About
                    </Link>
                    <Link :href="route('terms')" class="text-white font-bold text-sm sm:text-base hover:text-yellow-300 transition-colors">
                        Terms
                    </Link>
                    <Link :href="route('privacy')" class="text-white font-bold text-sm sm:text-base hover:text-yellow-300 transition-colors">
                        Privacy
                    </Link>

                    <!-- Auth Links -->
                    <template v-if="$page.props.auth.user">
                        <Link :href="route('dashboard')" class="text-white font-bold text-sm sm:text-base hover:text-yellow-300 transition-colors">
                            Dashboard
                        </Link>
                        <Link :href="route('logout')" method="post" as="button" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-bold text-sm rounded-lg transition-colors">
                            Logout
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-bold text-sm rounded-lg transition-colors">
                            Login
                        </Link>
                    </template>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="pt-32 pb-12 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Two Column Grid Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <!-- Left Column: Puppy -->
                    <div class="flex justify-center items-center">
                        <div class="h-[70vh] w-auto max-w-full">
                            <PuppyAnimation />
                        </div>
                    </div>

                    <!-- Right Column: Content -->
                    <div class="space-y-6">
                        <CartoonCard>
                            <div v-html="content" class="prose prose-lg max-w-none"></div>
                        </CartoonCard>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
