<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import SkyGrassBackground from '@/Components/Animations/SkyGrassBackground.vue';
import CloudsAnimation from '@/Components/Animations/CloudsAnimation.vue';
import PuppyAnimation from '@/Components/Animations/PuppyAnimation.vue';
import CartoonCard from '@/Components/Animations/CartoonCard.vue';

defineProps({
    content: String,
});

// Scroll detection for header background
const isScrolled = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <Head title="About" />

    <div class="animated-page">
        <!-- Animated Background -->
        <SkyGrassBackground />
        <CloudsAnimation />

        <!-- Header with Logo and Menu -->
        <div class="fixed top-0 left-0 right-0 z-50 py-4 px-4 sm:px-6 lg:px-8 transition-all duration-300"
             :class="{ 'bg-white shadow-lg': isScrolled }">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <!-- Logo -->
                <Link :href="route('home')" class="cursor-pointer">
                    <img src="/images/logo.webp" alt="Poop Bag Trivia" class="h-20 sm:h-24 w-auto">
                </Link>

                <!-- Navigation Menu -->
                <nav class="flex gap-4 sm:gap-6 items-center">
                    <Link :href="route('about')" class="font-bold text-sm sm:text-base transition-colors"
                          :class="isScrolled ? 'text-yellow-500 hover:text-yellow-600' : 'text-yellow-300'">
                        About
                    </Link>
                    <Link :href="route('terms')" class="font-bold text-sm sm:text-base transition-colors"
                          :class="isScrolled ? 'text-gray-800 hover:text-gray-600' : 'text-white hover:text-yellow-300'">
                        Terms
                    </Link>
                    <Link :href="route('privacy')" class="font-bold text-sm sm:text-base transition-colors"
                          :class="isScrolled ? 'text-gray-800 hover:text-gray-600' : 'text-white hover:text-yellow-300'">
                        Privacy
                    </Link>

                    <!-- Auth Links -->
                    <template v-if="$page.props.auth.user">
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

        <!-- Fixed Puppy on Left (Desktop only) -->
        <div class="hidden lg:block fixed left-0 top-0 bottom-0 w-1/2 z-10 pointer-events-none">
            <div class="h-full flex items-center justify-center">
                <div class="h-[70vh] w-auto max-w-full pointer-events-auto">
                    <PuppyAnimation />
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="pt-32 pb-12 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Mobile Puppy (shows on small screens, scrolls normally) -->
                <div class="lg:hidden flex items-center justify-center mb-8">
                    <div class="h-[50vh] w-auto max-w-full">
                        <PuppyAnimation />
                    </div>
                </div>

                <!-- Content Column (Right side on desktop, full width on mobile) -->
                <div class="lg:ml-[50%] lg:pl-8">
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
