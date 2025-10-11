<script setup>
import { Link } from '@inertiajs/vue3';
import SkyGrassBackground from '@/Components/Animations/SkyGrassBackground.vue';
import CloudsAnimation from '@/Components/Animations/CloudsAnimation.vue';
import PuppyAnimation from '@/Components/Animations/PuppyAnimation.vue';
import CartoonCard from '@/Components/Animations/CartoonCard.vue';
</script>

<template>
    <div class="animated-page">
        <SkyGrassBackground />
        <CloudsAnimation />

        <!-- Header with Logo -->
        <div class="fixed top-0 left-0 right-0 z-50 py-4 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <Link :href="route('home')" class="cursor-pointer">
                    <img src="/images/logo.webp" alt="Poop Bag Trivia" class="h-20 sm:h-24 w-auto">
                </Link>

                <nav class="flex gap-4 sm:gap-6">
                    <Link :href="route('about')" class="text-white font-bold text-sm sm:text-base hover:text-yellow-300 transition-colors">About</Link>
                    <Link :href="route('terms')" class="text-white font-bold text-sm sm:text-base hover:text-yellow-300 transition-colors">Terms</Link>
                    <Link :href="route('privacy')" class="text-white font-bold text-sm sm:text-base hover:text-yellow-300 transition-colors">Privacy</Link>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="pt-32 pb-12 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <!-- Left Column: Puppy -->
                    <div class="flex justify-center items-center">
                        <div class="h-[70vh] w-auto max-w-full">
                            <PuppyAnimation />
                        </div>
                    </div>

                    <!-- Right Column: Auth Form -->
                    <div class="space-y-6">
                        <CartoonCard>
                            <slot />
                        </CartoonCard>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
