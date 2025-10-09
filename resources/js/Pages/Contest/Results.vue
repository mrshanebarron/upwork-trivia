<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    question: Object,
    user_submission: Object,
    winner: Object,
    stats: Object,
});
</script>

<template>
    <Head :title="`Results - ${question.question_text.substring(0, 50)}...`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Question Results
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Question Display -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ question.question_text }}</h3>

                        <!-- Answer Options -->
                        <div class="space-y-3 mb-6">
                            <div v-for="option in ['A', 'B', 'C', 'D']" :key="option"
                                 class="flex items-center p-4 border rounded-lg"
                                 :class="{
                                     'bg-green-50 border-green-500': option === question.correct_answer,
                                     'bg-red-50 border-red-500': user_submission && option === user_submission.selected_answer && !user_submission.is_correct,
                                     'bg-gray-50': option !== question.correct_answer && !(user_submission && option === user_submission.selected_answer)
                                 }">
                                <span class="font-semibold text-lg mr-3">{{ option }}.</span>
                                <span class="flex-1">{{ question['option_' + option.toLowerCase()] }}</span>
                                <span v-if="option === question.correct_answer" class="text-green-600 font-semibold ml-2">
                                    ✓ Correct Answer
                                </span>
                                <span v-else-if="user_submission && option === user_submission.selected_answer"
                                      class="text-red-600 font-semibold ml-2">
                                    ✗ Your Answer
                                </span>
                            </div>
                        </div>

                        <!-- Explanation -->
                        <div v-if="question.explanation" class="p-4 bg-blue-50 border-l-4 border-blue-500">
                            <p class="text-sm font-semibold text-blue-900 mb-1">Explanation:</p>
                            <p class="text-sm text-blue-800">{{ question.explanation }}</p>
                        </div>
                    </div>
                </div>

                <!-- Winner Announcement -->
                <div v-if="winner" class="overflow-hidden bg-gradient-to-r from-yellow-400 to-yellow-500 shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-center">
                        <div class="text-4xl mb-2">🏆</div>
                        <h3 class="text-xl font-bold text-white mb-2">Winner Announcement</h3>
                        <p v-if="user_submission?.is_winner" class="text-white text-lg">
                            🎉 Congratulations! You won ${{ winner.prize_amount }}!
                        </p>
                        <p v-else class="text-white">
                            {{ winner.user.name }} won this question in {{ winner.response_time }}s
                        </p>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-blue-50">
                            <p class="text-sm text-gray-600">Total Submissions</p>
                            <p class="text-3xl font-bold text-blue-600">{{ stats.total_submissions }}</p>
                        </div>
                    </div>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-green-50">
                            <p class="text-sm text-gray-600">Correct Answers</p>
                            <p class="text-3xl font-bold text-green-600">{{ stats.correct_submissions }}</p>
                        </div>
                    </div>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-purple-50">
                            <p class="text-sm text-gray-600">Accuracy Rate</p>
                            <p class="text-3xl font-bold text-purple-600">{{ stats.accuracy }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Your Submission Details -->
                <div v-if="user_submission" class="overflow-hidden bg-white shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Submission</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 rounded-lg"
                                 :class="user_submission.is_correct ? 'bg-green-50' : 'bg-red-50'">
                                <p class="text-sm text-gray-600">Result</p>
                                <p class="text-xl font-bold"
                                   :class="user_submission.is_correct ? 'text-green-600' : 'text-red-600'">
                                    {{ user_submission.is_correct ? '✓ Correct' : '✗ Incorrect' }}
                                </p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">Your Answer</p>
                                <p class="text-xl font-bold text-gray-900">{{ user_submission.selected_answer }}</p>
                            </div>
                            <div v-if="user_submission.response_time" class="p-4 bg-blue-50 rounded-lg">
                                <p class="text-sm text-gray-600">Response Time</p>
                                <p class="text-xl font-bold text-blue-600">{{ user_submission.response_time }}s</p>
                            </div>
                            <div class="p-4 bg-purple-50 rounded-lg">
                                <p class="text-sm text-gray-600">Submitted At</p>
                                <p class="text-sm font-semibold text-purple-600">{{ user_submission.created_at }}</p>
                            </div>
                        </div>

                        <div v-if="user_submission.is_winner" class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-400">
                            <p class="text-yellow-800">
                                🏆 You won ${{ winner.prize_amount }}! Check your email for gift card details.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-center gap-4">
                    <Link :href="route('contest.show')"
                          class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                        Try Today's Question
                    </Link>
                    <Link :href="route('dashboard.submissions')"
                          class="px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition">
                        View All Submissions
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
