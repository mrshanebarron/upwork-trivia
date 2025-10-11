<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import CartoonCard from '@/Components/Animations/CartoonCard.vue';

defineProps({
    user: Object,
    winners: Object,
    recent_submissions: Array,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Page Title -->
            <CartoonCard variant="primary">
                <h1 class="text-4xl font-bold text-center text-gray-900">Your Dashboard</h1>
                <p class="text-center text-gray-700 mt-2">Welcome back, {{ $page.props.auth?.user?.name }}!</p>
            </CartoonCard>

            <!-- User Stats -->
            <CartoonCard>
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Your Stats</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-100/50 p-5 rounded-xl border-2 border-blue-300">
                        <p class="text-sm font-semibold text-gray-700">Total Winnings</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2">${{ user.total_winnings }}</p>
                    </div>
                    <div class="bg-green-100/50 p-5 rounded-xl border-2 border-green-300">
                        <p class="text-sm font-semibold text-gray-700">Can Participate</p>
                        <p class="text-3xl font-bold mt-2" :class="user.can_win ? 'text-green-600' : 'text-red-600'">
                            {{ user.can_win ? 'Yes ✓' : 'No ✗' }}
                        </p>
                    </div>
                    <div class="bg-purple-100/50 p-5 rounded-xl border-2 border-purple-300">
                        <p class="text-sm font-semibold text-gray-700">Last Won</p>
                        <p class="text-xl font-bold text-purple-600 mt-2">
                            {{ user.last_won_at || 'Never' }}
                        </p>
                    </div>
                </div>
                <div v-if="!user.can_win" class="mt-6 p-4 bg-yellow-100/60 border-l-4 border-yellow-500 rounded">
                    <p class="text-sm font-semibold text-yellow-800">
                        ⏰ You won recently! You'll be eligible again on {{ user.next_eligible_date }}
                    </p>
                </div>
            </CartoonCard>

            <!-- Quick Links -->
            <CartoonCard>
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Quick Links</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <Link :href="route('contest.show')"
                          class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 font-bold text-center transition-all duration-200 transform hover:scale-105 shadow-lg">
                        🏆 Play Golden Question
                    </Link>
                    <Link :href="route('dashboard.winnings')"
                          class="px-6 py-4 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl hover:from-purple-600 hover:to-purple-700 font-bold text-center transition-all duration-200 transform hover:scale-105 shadow-lg">
                        💰 View Winnings History
                    </Link>
                    <Link :href="route('dashboard.gift-cards')"
                          class="px-6 py-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 font-bold text-center transition-all duration-200 transform hover:scale-105 shadow-lg">
                        🎁 My Gift Cards
                    </Link>
                </div>
            </CartoonCard>

            <!-- Recent Wins -->
            <CartoonCard v-if="winners.data.length > 0" variant="success">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">🎉 Recent Wins</h3>
                <div class="space-y-3">
                    <div v-for="winner in winners.data" :key="winner.id"
                         class="p-4 bg-white/60 border-2 border-green-300 rounded-xl hover:bg-white/80 transition-all duration-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-bold text-gray-900 text-lg">
                                    💵 ${{ winner.prize_amount }} - {{ winner.daily_question.question_text.substring(0, 60) }}...
                                </p>
                                <p class="text-sm text-gray-700 mt-1">
                                    Gift Card:
                                    <span class="font-semibold" :class="{
                                        'text-green-600': winner.gift_card?.status === 'delivered',
                                        'text-yellow-600': winner.gift_card?.status === 'pending',
                                        'text-red-600': winner.gift_card?.status === 'failed'
                                    }">
                                        {{ winner.gift_card?.status || 'Processing' }}
                                    </span>
                                </p>
                            </div>
                            <span class="text-sm text-gray-600 font-medium">{{ winner.created_at }}</span>
                        </div>
                    </div>
                </div>
            </CartoonCard>

            <!-- Recent Submissions -->
            <CartoonCard v-if="recent_submissions.length > 0">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">📝 Recent Submissions</h3>
                <div class="space-y-3">
                    <div v-for="submission in recent_submissions" :key="submission.id"
                         class="p-4 bg-white/60 border-2 rounded-xl flex justify-between items-center"
                         :class="submission.is_correct ? 'border-green-300' : 'border-red-300'">
                        <span class="text-gray-900 font-medium">{{ submission.daily_question.question_text.substring(0, 50) }}...</span>
                        <span class="font-bold text-lg" :class="submission.is_correct ? 'text-green-600' : 'text-red-600'">
                            {{ submission.is_correct ? '✓ Correct' : '✗ Incorrect' }}
                        </span>
                    </div>
                </div>
            </CartoonCard>
        </div>
    </AuthenticatedLayout>
</template>
