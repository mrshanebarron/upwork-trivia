<script setup>
import { Head, Link } from '@inertiajs/vue3';
import SkyGrassBackground from '@/Components/Animations/SkyGrassBackground.vue';
import CloudsAnimation from '@/Components/Animations/CloudsAnimation.vue';
import CartoonCard from '@/Components/Animations/CartoonCard.vue';

const props = defineProps({
    trivia_code: Object,
    selected_answer: String,
    correct_answer: String,
    is_correct: Boolean,
});
</script>

<template>
    <Head title="Bag Trivia Results" />

    <div class="animated-contest-page">
        <!-- Animated Background -->
        <SkyGrassBackground />
        <CloudsAnimation />

        <!-- Header with Logo -->
        <div class="fixed top-0 left-0 right-0 z-50 py-4 px-4 sm:px-6 lg:px-8 bg-white shadow-lg">
            <div class="max-w-7xl mx-auto flex justify-center">
                <Link :href="route('home')" class="cursor-pointer">
                    <img src="/images/logo.webp" alt="Poop Bag Trivia" class="h-20 sm:h-24 w-auto">
                </Link>
            </div>
        </div>

        <!-- Main Content -->
        <div class="pt-32 pb-12 relative z-10">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Results Card -->
                <CartoonCard :variant="is_correct ? 'success' : 'primary'">
                    <div class="text-center space-y-6">
                        <!-- Result Icon -->
                        <div class="text-6xl">
                            {{ is_correct ? '✅' : '❌' }}
                        </div>

                        <!-- Result Title -->
                        <h1 class="text-4xl font-bold">
                            {{ is_correct ? 'Correct!' : 'Not Quite!' }}
                        </h1>

                        <!-- Result Message -->
                        <p class="text-xl text-gray-800">
                            {{ is_correct ? 'Great job! You got it right!' : 'Don\'t worry, try another bag!' }}
                        </p>

                        <!-- Answer Details -->
                        <div class="space-y-4 py-6">
                            <div class="p-4 bg-white/60 backdrop-blur-sm rounded-xl">
                                <p class="text-sm text-gray-600 mb-1">You selected:</p>
                                <p class="text-lg font-bold text-gray-900">{{ selected_answer }}</p>
                            </div>

                            <div v-if="!is_correct" class="p-4 bg-green-100 border-2 border-green-500 rounded-xl">
                                <p class="text-sm text-green-800 mb-1">Correct answer:</p>
                                <p class="text-lg font-bold text-green-900">{{ correct_answer }}</p>
                            </div>
                        </div>

                        <!-- Educational Message -->
                        <div class="p-6 bg-blue-50 rounded-xl border-2 border-blue-200">
                            <p class="text-lg font-semibold text-blue-900 mb-2">Thanks for playing!</p>
                            <p class="text-gray-700">Bag trivia is just for fun. Want to win real prizes?</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-4 pt-4">
                            <Link :href="route('home')" class="block w-full py-4 px-6 bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-bold text-lg rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                                Try Today's Golden Question & Win $10!
                            </Link>

                            <Link :href="route('trivia.show', { code: trivia_code.code })" class="block w-full py-3 px-6 bg-white hover:bg-gray-50 text-gray-900 font-bold rounded-xl transition-all duration-200 border-2 border-gray-300">
                                Try Another Question from This Bag
                            </Link>
                        </div>
                    </div>
                </CartoonCard>
            </div>
        </div>
    </div>
</template>
