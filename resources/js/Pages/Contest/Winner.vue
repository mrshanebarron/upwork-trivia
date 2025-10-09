<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    submission: Object,
    winner: Object,
    gift_card: Object,
});
</script>

<template>
    <Head title="Congratulations!" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Celebration Banner -->
                <div class="bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 rounded-lg shadow-xl p-8 mb-6 text-center">
                    <div class="text-6xl mb-4">🎉🏆🎉</div>
                    <h1 class="text-4xl font-bold text-white mb-2">Congratulations!</h1>
                    <p class="text-xl text-white">You Won the Golden Question!</p>
                </div>

                <!-- Prize Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Prize Details</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
                                <span class="text-gray-700">Prize Amount:</span>
                                <span class="text-2xl font-bold text-green-600">${{ winner.prize_amount }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
                                <span class="text-gray-700">Gift Card Status:</span>
                                <span class="font-semibold capitalize"
                                      :class="{
                                          'text-green-600': gift_card?.status === 'delivered',
                                          'text-yellow-600': gift_card?.status === 'pending',
                                          'text-red-600': gift_card?.status === 'failed'
                                      }">
                                    {{ gift_card?.status || 'Processing' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gift Card Info -->
                <div v-if="gift_card" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Your Gift Card</h2>
                        
                        <div v-if="gift_card.status === 'delivered'" class="space-y-4">
                            <p class="text-gray-700">Your gift card has been delivered! Use the link below to redeem it.</p>
                            <a v-if="gift_card.redemption_link"
                               :href="gift_card.redemption_link"
                               target="_blank"
                               class="inline-block px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                                Redeem Your Gift Card →
                            </a>
                            <p class="text-sm text-gray-600">
                                Need help? Contact Tremendous support at support@tremendous.com
                            </p>
                        </div>

                        <div v-else-if="gift_card.status === 'pending'" class="p-4 bg-yellow-50 border-l-4 border-yellow-400">
                            <p class="text-yellow-800">
                                Your gift card is being processed. You'll receive an email with redemption instructions shortly.
                            </p>
                        </div>

                        <div v-else-if="gift_card.status === 'failed'" class="p-4 bg-red-50 border-l-4 border-red-400">
                            <p class="text-red-800">
                                There was an issue processing your gift card. Our team has been notified and will resolve this shortly.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Question Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Winning Question</h2>
                        <p class="text-lg text-gray-900 mb-3">{{ submission.daily_question.question_text }}</p>
                        <div class="p-3 bg-green-50 border-l-4 border-green-500">
                            <p class="text-sm text-gray-700">
                                <strong>Your Answer:</strong> {{ submission.selected_answer }}
                            </p>
                            <p class="text-sm text-green-700 mt-1">✓ Correct!</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-center gap-4">
                    <Link :href="route('dashboard')"
                          class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                        Go to Dashboard
                    </Link>
                    <Link :href="route('contest.show')"
                          class="px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition">
                        View Today's Question
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
