<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    submissions: Object, // Paginated collection
    stats: Object,
});
</script>

<template>
    <Head title="My Submissions" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                My Submissions
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-blue-50">
                            <p class="text-sm text-gray-600">Total Submissions</p>
                            <p class="text-3xl font-bold text-blue-600">{{ stats.total }}</p>
                        </div>
                    </div>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-green-50">
                            <p class="text-sm text-gray-600">Correct</p>
                            <p class="text-3xl font-bold text-green-600">{{ stats.correct }}</p>
                        </div>
                    </div>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-red-50">
                            <p class="text-sm text-gray-600">Incorrect</p>
                            <p class="text-3xl font-bold text-red-600">{{ stats.incorrect }}</p>
                        </div>
                    </div>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-purple-50">
                            <p class="text-sm text-gray-600">Accuracy</p>
                            <p class="text-3xl font-bold text-purple-600">{{ stats.accuracy }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Submissions List -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Submission History</h3>

                        <div v-if="submissions.data.length === 0" class="text-center py-8">
                            <p class="text-gray-600">No submissions yet. Start playing!</p>
                            <Link :href="route('contest.show')"
                                  class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Play Golden Question
                            </Link>
                        </div>

                        <div v-else class="space-y-3">
                            <div v-for="submission in submissions.data" :key="submission.id"
                                 class="border rounded-lg p-4 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 mb-1">
                                            {{ submission.daily_question.question_text }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Submitted on {{ submission.created_at }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 ml-4">
                                        <span v-if="submission.is_winner"
                                              class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                            🏆 Winner
                                        </span>
                                        <span :class="{
                                                  'px-3 py-1 text-sm font-semibold rounded-full': true,
                                                  'bg-green-100 text-green-800': submission.is_correct,
                                                  'bg-red-100 text-red-800': !submission.is_correct
                                              }">
                                            {{ submission.is_correct ? '✓ Correct' : '✗ Incorrect' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                    <div class="p-3 bg-gray-50 rounded">
                                        <p class="text-xs text-gray-600">Your Answer</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ submission.selected_answer }}</p>
                                    </div>
                                    <div class="p-3 rounded"
                                         :class="submission.is_correct ? 'bg-green-50' : 'bg-blue-50'">
                                        <p class="text-xs text-gray-600">Correct Answer</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ submission.daily_question.correct_answer }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Additional Details -->
                                <div class="mt-3 pt-3 border-t flex flex-wrap gap-4 text-xs text-gray-600">
                                    <div v-if="submission.response_time">
                                        <strong>Response Time:</strong> {{ submission.response_time }}s
                                    </div>
                                    <div v-if="submission.sticker">
                                        <strong>Scanned Sticker:</strong> {{ submission.sticker.identifier }}
                                    </div>
                                    <div v-if="submission.latitude && submission.longitude">
                                        <strong>Location:</strong> {{ submission.latitude }}, {{ submission.longitude }}
                                    </div>
                                </div>

                                <!-- View Results Link -->
                                <div class="mt-3">
                                    <Link :href="route('contest.results', submission.daily_question.id)"
                                          class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                        View Question Details →
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="submissions.links.length > 3" class="mt-6 flex justify-center gap-2">
                            <Link v-for="link in submissions.links" :key="link.label"
                                  :href="link.url"
                                  :class="{
                                      'px-4 py-2 rounded-lg': true,
                                      'bg-blue-600 text-white': link.active,
                                      'bg-gray-200 text-gray-700 hover:bg-gray-300': !link.active && link.url,
                                      'bg-gray-100 text-gray-400 cursor-not-allowed': !link.url
                                  }"
                                  :disabled="!link.url"
                                  v-html="link.label">
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
