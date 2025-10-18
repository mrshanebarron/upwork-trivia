<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, router, Link, usePage } from '@inertiajs/vue3';
import SkyGrassBackground from '@/Components/Animations/SkyGrassBackground.vue';
import CloudsAnimation from '@/Components/Animations/CloudsAnimation.vue';
import PuppyAnimation from '@/Components/Animations/PuppyAnimation.vue';
import CartoonCard from '@/Components/Animations/CartoonCard.vue';
import { useRecaptcha } from '@/composables/useRecaptcha';

const props = defineProps({
    question: Object,
    sticker: Object,
    is_authenticated: Boolean,
    can_submit: Boolean,
    already_submitted: Boolean,
    ad_boxes: Array,
});

const page = usePage();
const selectedAnswer = ref(null);
const submitting = ref(false);
const { execute: executeRecaptcha } = useRecaptcha();

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
            sticker_id: props.sticker?.id,
            latitude: null,
            longitude: null,
            device_fingerprint: null,
            recaptcha_token: recaptchaToken,
            // Honeypot fields
            website: honeypot.value.website,
            email_hp: honeypot.value.email, // Renamed to avoid collision with real email field
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
    // Anyone can submit if they've selected an answer
    // Backend will handle eligibility checks
    return selectedAnswer.value !== null;
});

const submitButtonText = computed(() => {
    if (submitting.value) return 'Submitting...';
    return 'Submit Answer 🚀';
});
</script>

<template>
    <Head title="Golden Question Contest" />

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
                        <!-- Contest Header -->
                        <CartoonCard variant="primary">
                            <div class="text-center">
                                <h1 class="text-4xl font-bold mb-2">Golden Question Contest</h1>
                                <p class="text-xl text-gray-800">First correct answer wins $10!</p>
                            </div>
                        </CartoonCard>

                        <!-- Flash Messages -->
                        <div v-if="flashMessage"
                             class="relative rounded-xl border-2 p-4 shadow-lg"
                             :class="{
                                 'bg-green-50 border-green-500 text-green-800': flashType === 'success',
                                 'bg-red-50 border-red-500 text-red-800': flashType === 'error',
                                 'bg-blue-50 border-blue-500 text-blue-800': flashType === 'info'
                             }">
                            <div class="flex justify-between items-start">
                                <p class="text-lg font-semibold">{{ flashMessage }}</p>
                                <button @click="dismissFlash"
                                        class="ml-4 text-gray-500 hover:text-gray-700 font-bold text-xl">
                                    &times;
                                </button>
                            </div>
                        </div>

                            <!-- Question Card Container -->
                        <div v-if="question">
                            <div v-if="!question" class="text-center py-8">
                                <CartoonCard>
                                    <p class="text-lg text-gray-700">No active question available. Check back soon!</p>
                                </CartoonCard>
                            </div>

                            <div v-else-if="question.has_winner">
                                <CartoonCard variant="success">
                                    <p class="text-lg text-gray-800">🎉 Today's question already has a winner! Check back tomorrow for a new question.</p>
                                </CartoonCard>
                            </div>

                            <CartoonCard v-else>
                                <div class="mb-6">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ question.question_text }}</h2>
                                </div>

                                <div class="space-y-3 mb-6">
                                    <label v-for="option in ['A', 'B', 'C', 'D']" :key="option"
                                           class="flex items-center p-4 bg-white/40 backdrop-blur-sm border-2 border-white/50 rounded-xl cursor-pointer transition-all duration-200"
                                           :class="{ 'bg-blue-100/60 border-blue-500': selectedAnswer === option }">
                                        <input type="radio" :value="option" v-model="selectedAnswer" class="h-5 w-5 text-blue-600">
                                        <span class="ml-3 text-gray-900 text-lg">
                                            <strong class="text-blue-600">{{ option }}.</strong> {{ question['option_' + option.toLowerCase()] }}
                                        </span>
                                    </label>
                                </div>

                                <!-- Honeypot fields - hidden from users, visible to bots -->
                                <div style="position: absolute; left: -5000px;" aria-hidden="true">
                                    <input type="text" v-model="honeypot.website" name="website" tabindex="-1" autocomplete="off">
                                    <input type="email" v-model="honeypot.email" name="email" tabindex="-1" autocomplete="off">
                                    <input type="checkbox" v-model="honeypot.subscribe" name="subscribe" tabindex="-1">
                                </div>

                                <div class="flex justify-center">
                                    <button @click="submitAnswer" :disabled="!canSubmit || submitting"
                                            class="px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xl font-bold rounded-xl shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 hover:from-blue-600 hover:to-blue-700 transform hover:scale-105">
                                        {{ submitButtonText }}
                                    </button>
                                </div>
                            </CartoonCard>
                        </div>

                        <!-- No Question Message -->
                        <div v-if="!question" class="text-center py-8">
                            <CartoonCard>
                                <p class="text-lg text-gray-700">No active question available. Check back soon!</p>
                            </CartoonCard>
                        </div>
                    </div>
                </div>

                <!-- Advertisement Boxes -->
                <div v-if="ad_boxes && ad_boxes.length > 0" class="mt-12 space-y-6">
                    <div v-for="ad in ad_boxes" :key="ad.id">
                        <CartoonCard>
                            <a :href="ad.url" target="_blank" rel="noopener noreferrer" class="block">
                                <div class="text-center">
                                    <div class="text-sm text-gray-500 font-bold mb-2">ADVERTISEMENT</div>
                                    <div v-html="ad.html_content" class="ad-content"></div>
                                </div>
                            </a>
                        </CartoonCard>
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
