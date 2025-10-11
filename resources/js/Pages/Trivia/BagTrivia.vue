<script setup>
import { ref, computed } from 'vue';
import { Head, router, Link, usePage } from '@inertiajs/vue3';
import SkyGrassBackground from '@/Components/Animations/SkyGrassBackground.vue';
import CloudsAnimation from '@/Components/Animations/CloudsAnimation.vue';
import PuppyAnimation from '@/Components/Animations/PuppyAnimation.vue';
import CartoonCard from '@/Components/Animations/CartoonCard.vue';
import { useRecaptcha } from '@/composables/useRecaptcha';

const props = defineProps({
    trivia_code: Object,
    question: Object,
    is_authenticated: Boolean,
    can_submit: Boolean,
    already_submitted: Boolean,
    ad_boxes: Array,
});

const page = usePage();
const selectedAnswer = ref(null);
const submitting = ref(false);
const { execute: executeRecaptcha } = useRecaptcha();

// Flash message state
const flashMessage = ref(null);
const flashType = ref(null);

// Watch for flash messages from backend
if (page.props.flash) {
    if (page.props.flash.success) {
        flashMessage.value = page.props.flash.success;
        flashType.value = 'success';
    } else if (page.props.flash.error) {
        flashMessage.value = page.props.flash.error;
        flashType.value = 'error';
    } else if (page.props.flash.info) {
        flashMessage.value = page.props.flash.info;
        flashType.value = 'info';
    }
}

const dismissFlash = () => {
    flashMessage.value = null;
    flashType.value = null;
};

// Honeypot fields - should remain empty for real users
const honeypot = ref({
    website: '',
    email: '',
    subscribe: false,
});

const submitAnswer = async () => {
    if (!selectedAnswer.value || submitting.value) return;

    submitting.value = true;

    try {
        // Execute reCAPTCHA before submission
        const recaptchaToken = await executeRecaptcha('contest_submit');

        router.post(route('contest.submit'), {
            question_id: props.question.id,
            answer: selectedAnswer.value,
            sticker_id: null, // No sticker for bag codes
            latitude: null,
            longitude: null,
            device_fingerprint: null,
            recaptcha_token: recaptchaToken,
            // Honeypot fields
            website: honeypot.value.website,
            email_hp: honeypot.value.email,
            subscribe: honeypot.value.subscribe,
        }, {
            onFinish: () => {
                submitting.value = false;
            }
        });
    } catch (error) {
        console.error('Submission error:', error);
        submitting.value = false;
    }
};

const canSubmit = computed(() => {
    return selectedAnswer.value !== null;
});

const submitButtonText = computed(() => {
    if (submitting.value) return 'Submitting...';
    return 'Submit Answer';
});
</script>

<template>
    <Head :title="`Bag Trivia - Code ${trivia_code.code}`" />

    <div class="animated-contest-page">
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
                    <Link :href="route('about')" class="text-white font-bold text-sm sm:text-base hover:text-yellow-300 transition-colors">
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

        <!-- Fixed Puppy (Desktop Only) -->
        <div class="hidden lg:block fixed left-0 top-32 w-1/2 max-w-[592px] h-[calc(100vh-8rem)] z-10 pointer-events-none">
            <div class="flex justify-center items-center h-full px-4">
                <div class="h-[60vh] w-auto pointer-events-auto">
                    <PuppyAnimation />
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="pt-32 pb-12 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Two Column Grid Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column: Puppy (Spacer) -->
                    <div class="hidden lg:block">
                        <!-- Empty spacer for grid layout -->
                    </div>

                    <!-- Right Column: Content -->
                    <div class="space-y-6">
                        <!-- Golden Question Teaser -->
                        <div v-if="question && !question.has_winner">
                            <Link :href="route('home')">
                                <CartoonCard variant="primary" class="cursor-pointer hover:shadow-2xl transition-shadow duration-200">
                                    <div class="text-center">
                                        <h1 class="text-3xl font-bold mb-2">🏆 Today's Golden Question</h1>
                                        <p class="text-lg text-gray-800 mb-4">First correct answer wins ${{ question.prize_amount }}!</p>
                                        <div class="inline-flex items-center gap-2 px-6 py-3 bg-yellow-500 text-gray-900 font-bold rounded-xl hover:bg-yellow-600 transition-colors">
                                            <span>Click to Answer & Win</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-3">Available at participating locations</p>
                                    </div>
                                </CartoonCard>
                            </Link>
                        </div>

                        <!-- Winner Announcement -->
                        <div v-else-if="question && question.has_winner">
                            <CartoonCard variant="success">
                                <div class="text-center">
                                    <p class="text-lg text-gray-800 mb-2">Today's Golden Question already has a winner!</p>
                                    <p class="text-md text-gray-600">Check back tomorrow for a new question.</p>
                                </div>
                            </CartoonCard>
                        </div>

                        <!-- No Question Available -->
                        <div v-else-if="!question">
                            <CartoonCard>
                                <p class="text-lg text-gray-700 text-center">No Golden Question available today. Check back soon!</p>
                            </CartoonCard>
                        </div>

                        <!-- Divider -->
                        <div class="py-4">
                            <div class="border-t-4 border-white/50"></div>
                        </div>

                        <!-- First Advertisement (Above Trivia) -->
                        <div v-if="ad_boxes && ad_boxes.length > 0">
                            <CartoonCard>
                                <a :href="ad_boxes[0].url" target="_blank" rel="noopener noreferrer" class="block">
                                    <div class="text-center">
                                        <div class="text-sm text-gray-500 font-bold mb-2">ADVERTISEMENT</div>
                                        <div v-html="ad_boxes[0].html_content" class="ad-content"></div>
                                    </div>
                                </a>
                            </CartoonCard>
                        </div>

                        <!-- Bag Trivia Section -->
                        <CartoonCard variant="secondary">
                            <div class="text-center mb-6">
                                <h2 class="text-3xl font-bold mb-2">Your Bag's Trivia</h2>
                                <p class="text-lg text-gray-700">Code: <strong>{{ trivia_code.code }}</strong></p>
                                <p v-if="trivia_code.title" class="text-md text-gray-600 mt-1">{{ trivia_code.title }}</p>
                            </div>

                            <div v-if="trivia_code.description" class="mb-6 p-4 bg-white/40 backdrop-blur-sm rounded-xl">
                                <p class="text-gray-800">{{ trivia_code.description }}</p>
                            </div>

                            <div class="space-y-4">
                                <div v-for="(answer, index) in trivia_code.answers" :key="answer.id"
                                     class="p-4 bg-white/60 backdrop-blur-sm rounded-xl border-2 border-white/70">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold mr-3">
                                            {{ index + 1 }}
                                        </div>
                                        <p class="text-lg text-gray-900 flex-1">{{ answer.text }}</p>
                                    </div>
                                </div>
                            </div>
                        </CartoonCard>

                        <!-- Second Advertisement (Below Trivia) -->
                        <div v-if="ad_boxes && ad_boxes.length > 1">
                            <CartoonCard>
                                <a :href="ad_boxes[1].url" target="_blank" rel="noopener noreferrer" class="block">
                                    <div class="text-center">
                                        <div class="text-sm text-gray-500 font-bold mb-2">ADVERTISEMENT</div>
                                        <div v-html="ad_boxes[1].html_content" class="ad-content"></div>
                                    </div>
                                </a>
                            </CartoonCard>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.ad-content :deep(h3) {
    @apply text-2xl font-bold text-gray-900 mb-2;
}

.ad-content :deep(p) {
    @apply text-lg text-gray-700;
}

.ad-content :deep(.ad-banner) {
    @apply p-4 rounded-lg bg-gradient-to-r from-blue-50 to-purple-50;
}
</style>
