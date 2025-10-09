<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SkyGrassBackground from '@/Components/Animations/SkyGrassBackground.vue';
import CloudsAnimation from '@/Components/Animations/CloudsAnimation.vue';
import PlaneAnimation from '@/Components/Animations/PlaneAnimation.vue';
import PuppyAnimation from '@/Components/Animations/PuppyAnimation.vue';
import GlassmorphismCard from '@/Components/Animations/GlassmorphismCard.vue';
import { useRecaptcha } from '@/composables/useRecaptcha';

const props = defineProps({
    question: Object,
    sticker: Object,
    can_submit: Boolean,
    already_submitted: Boolean,
});

const selectedAnswer = ref(null);
const submitting = ref(false);
const { execute: executeRecaptcha } = useRecaptcha();

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
    return props.can_submit && !props.already_submitted && selectedAnswer.value;
});
</script>

<template>
    <Head title="Golden Question Contest" />

    <div class="animated-contest-page">
        <!-- Animated Background -->
        <SkyGrassBackground />
        <CloudsAnimation />
        <PlaneAnimation />

        <!-- Main Content with Layout -->
        <AuthenticatedLayout>
            <div class="py-12 relative z-10">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <!-- Contest Header with Glassmorphism -->
                    <GlassmorphismCard variant="primary" class="mb-6">
                        <div class="text-center">
                            <h1 class="text-4xl font-bold mb-2">🏆 Golden Question Contest</h1>
                            <p class="text-xl text-gray-800">First correct answer wins $10!</p>
                        </div>
                    </GlassmorphismCard>

                    <!-- Puppy and Question Card Container -->
                    <div v-if="question" class="flex flex-col lg:flex-row items-start gap-6 mb-6">
                        <!-- Animated Puppy -->
                        <div class="hidden lg:block lg:w-1/4">
                            <PuppyAnimation />
                        </div>

                        <!-- Question Card -->
                        <div class="w-full lg:w-3/4">
                            <div v-if="!question" class="text-center py-8">
                                <GlassmorphismCard>
                                    <p class="text-lg text-gray-700">No active question available. Check back soon!</p>
                                </GlassmorphismCard>
                            </div>

                            <div v-else-if="question.has_winner">
                                <GlassmorphismCard variant="success">
                                    <p class="text-lg text-gray-800">🎉 Today's question already has a winner! Check back tomorrow for a new question.</p>
                                </GlassmorphismCard>
                            </div>

                            <GlassmorphismCard v-else>
                                <div class="mb-6">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ question.question_text }}</h2>
                                </div>

                                <div class="space-y-3 mb-6">
                                    <label v-for="option in ['A', 'B', 'C', 'D']" :key="option"
                                           class="flex items-center p-4 bg-white/40 backdrop-blur-sm border-2 border-white/50 rounded-xl cursor-pointer hover:bg-white/60 hover:border-blue-400 transition-all duration-200"
                                           :class="{ 'bg-blue-100/60 border-blue-500': selectedAnswer === option }">
                                        <input type="radio" :value="option" v-model="selectedAnswer" class="h-5 w-5 text-blue-600">
                                        <span class="ml-3 text-gray-900 text-lg">
                                            <strong class="text-blue-600">{{ option }}.</strong> {{ question['option_' + option.toLowerCase()] }}
                                        </span>
                                    </label>
                                </div>

                                <div v-if="!can_submit" class="mb-4 p-4 bg-yellow-100/60 backdrop-blur-sm border-l-4 border-yellow-500 rounded">
                                    <p class="text-yellow-800 font-semibold">
                                        {{ already_submitted ? 'You have already submitted an answer to this question!' : 'You are not eligible to submit at this time.' }}
                                    </p>
                                </div>

                                <div class="flex justify-center">
                                    <button @click="submitAnswer" :disabled="!canSubmit || submitting"
                                            class="px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xl font-bold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 transition-all duration-200">
                                        <span v-if="submitting">Submitting...</span>
                                        <span v-else>Submit Answer 🚀</span>
                                    </button>
                                </div>
                            </GlassmorphismCard>
                        </div>
                    </div>

                    <!-- No Question Message -->
                    <div v-if="!question" class="text-center py-8">
                        <GlassmorphismCard>
                            <p class="text-lg text-gray-700">No active question available. Check back soon!</p>
                        </GlassmorphismCard>
                    </div>

                    <!-- Mobile Puppy (below question on small screens) -->
                    <div v-if="question" class="lg:hidden flex justify-center mb-6">
                        <PuppyAnimation />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </div>
</template>
