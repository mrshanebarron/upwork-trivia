<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    user: Object,
    winners: Object,
    recent_submissions: Array,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- User Stats -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Stats</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Total Winnings</p>
                                <p class="text-2xl font-bold text-blue-600">${{ user.total_winnings }}</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Can Participate</p>
                                <p class="text-2xl font-bold" :class="user.can_win ? 'text-green-600' : 'text-red-600'">
                                    {{ user.can_win ? 'Yes' : 'No' }}
                                </p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Last Won</p>
                                <p class="text-lg font-semibold text-purple-600">
                                    {{ user.last_won_at || 'Never' }}
                                </p>
                            </div>
                        </div>
                        <div v-if="!user.can_win" class="mt-4 p-3 bg-yellow-50 border-l-4 border-yellow-400">
                            <p class="text-sm text-yellow-700">
                                You won recently! You'll be eligible again on {{ user.next_eligible_date }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Links</h3>
                        <div class="flex flex-wrap gap-4">
                            <Link :href="route('contest.show')"
                                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Play Golden Question
                            </Link>
                            <Link :href="route('dashboard.winnings')"
                                  class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                View Winnings History
                            </Link>
                            <Link :href="route('dashboard.gift-cards')"
                                  class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                My Gift Cards
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Recent Wins -->
                <div v-if="winners.data.length > 0" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Wins</h3>
                        <div class="space-y-3">
                            <div v-for="winner in winners.data" :key="winner.id"
                                 class="p-4 border rounded-lg hover:bg-gray-50 transition">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold text-gray-900">
                                            ${{ winner.prize_amount }} - {{ winner.daily_question.question_text.substring(0, 60) }}...
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Gift Card:
                                            <span :class="{
                                                'text-green-600': winner.gift_card?.status === 'delivered',
                                                'text-yellow-600': winner.gift_card?.status === 'pending',
                                                'text-red-600': winner.gift_card?.status === 'failed'
                                            }">
                                                {{ winner.gift_card?.status || 'Processing' }}
                                            </span>
                                        </p>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ winner.created_at }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Submissions -->
                <div v-if="recent_submissions.length > 0" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Submissions</h3>
                        <div class="space-y-2">
                            <div v-for="submission in recent_submissions" :key="submission.id"
                                 class="p-3 border rounded flex justify-between items-center">
                                <span class="text-gray-900">{{ submission.daily_question.question_text.substring(0, 50) }}...</span>
                                <span :class="submission.is_correct ? 'text-green-600' : 'text-red-600'">
                                    {{ submission.is_correct ? '✓ Correct' : '✗ Incorrect' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
