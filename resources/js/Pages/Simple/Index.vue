<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import SkyGrassBackground from '@/Components/Animations/SkyGrassBackground.vue';
import CloudsAnimation from '@/Components/Animations/CloudsAnimation.vue';
import PuppyAnimation from '@/Components/Animations/PuppyAnimation.vue';
import CartoonCard from '@/Components/Animations/CartoonCard.vue';

const props = defineProps({
    questions: Array,
    periodType: String,
    date: String,
});

const page = usePage();

// Track which answers are shown
const showAnswers = reactive({});

const toggleAnswer = (questionId) => {
    showAnswers[questionId] = !showAnswers[questionId];
};

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
    <Head :title="periodType === 'weekly' ? 'This Week\'s Questions' : 'Today\'s Questions'" />

    <div class="animated-contest-page">
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
                          :class="isScrolled ? 'text-gray-800 hover:text-gray-600' : 'text-white hover:text-yellow-300'">
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
                    <template v-if="$page.props.auth?.user">
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
                        <!-- Page Header -->
                        <CartoonCard variant="primary">
                            <div class="text-center">
                                <h1 class="text-4xl font-bold mb-2">
                                    {{ periodType === 'weekly' ? 'This Week\'s' : 'Today\'s' }} Questions
                                </h1>
                                <p class="text-xl text-gray-800">{{ date }}</p>
                            </div>
                        </CartoonCard>

                        <!-- No Questions Message -->
                        <CartoonCard v-if="questions.length === 0">
                            <div class="text-center py-8">
                                <p class="text-xl text-gray-700">
                                    No questions available for {{ periodType === 'weekly' ? 'this week' : 'today' }}.
                                </p>
                                <p class="text-gray-500 mt-2">
                                    Check back soon!
                                </p>
                            </div>
                        </CartoonCard>

                        <!-- Questions List -->
                        <div v-else class="space-y-6">
                            <CartoonCard v-for="question in questions" :key="question.id">
                                <!-- Question Number & Text -->
                                <div class="mb-4">
                                    <span class="inline-block bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full mb-3">
                                        Question {{ question.display_order }}
                                    </span>
                                    <h2 class="text-xl font-bold text-gray-900">
                                        {{ question.question }}
                                    </h2>
                                </div>

                                <!-- Answer Toggle Button -->
                                <div>
                                    <button
                                        @click="toggleAnswer(question.id)"
                                        class="px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-lg font-bold rounded-xl shadow-lg transition-all duration-200 hover:from-blue-600 hover:to-blue-700 transform hover:scale-105"
                                        :class="{ 'from-blue-700 to-blue-800': showAnswers[question.id] }"
                                    >
                                        <span v-if="!showAnswers[question.id]">Show Answer 👀</span>
                                        <span v-else>Hide Answer</span>
                                    </button>

                                    <!-- Answer Content -->
                                    <transition name="fade">
                                        <div v-if="showAnswers[question.id]" class="mt-4 p-4 bg-green-50/80 backdrop-blur-sm rounded-xl border-2 border-green-500/50">
                                            <p class="text-gray-900 font-medium text-lg">{{ question.answer }}</p>
                                        </div>
                                    </transition>
                                </div>
                            </CartoonCard>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
