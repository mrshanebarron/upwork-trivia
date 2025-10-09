<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    gift_cards: Object, // Paginated collection
});
</script>

<template>
    <Head title="My Gift Cards" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                My Gift Cards
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Info Banner -->
                <div class="overflow-hidden bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <p class="text-sm text-blue-700">
                        All gift cards are delivered via email through Tremendous. Check your inbox for redemption instructions.
                    </p>
                </div>

                <!-- Gift Cards List -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Gift Card History</h3>

                        <div v-if="gift_cards.data.length === 0" class="text-center py-8">
                            <p class="text-gray-600">No gift cards yet. Win the Golden Question to get started!</p>
                            <Link :href="route('contest.show')"
                                  class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Play Golden Question
                            </Link>
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="card in gift_cards.data" :key="card.id"
                                 class="border rounded-lg overflow-hidden">
                                <!-- Card Header -->
                                <div class="p-4 bg-gradient-to-r"
                                     :class="{
                                         'from-green-500 to-green-600': card.status === 'delivered',
                                         'from-yellow-500 to-yellow-600': card.status === 'pending',
                                         'from-red-500 to-red-600': card.status === 'failed'
                                     }">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="text-2xl font-bold text-white">${{ card.amount }} {{ card.currency }}</p>
                                            <p class="text-sm text-white">{{ card.provider }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-block px-3 py-1 bg-white bg-opacity-20 backdrop-blur-sm text-white text-sm font-semibold rounded-full">
                                                {{ card.status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="p-4">
                                    <div class="space-y-2 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Order ID:</span>
                                            <span class="text-gray-900 font-mono">{{ card.order_id }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Reward ID:</span>
                                            <span class="text-gray-900 font-mono">{{ card.reward_id }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Created:</span>
                                            <span class="text-gray-900">{{ card.created_at }}</span>
                                        </div>
                                        <div v-if="card.delivered_at" class="flex justify-between text-sm">
                                            <span class="text-gray-600">Delivered:</span>
                                            <span class="text-gray-900">{{ card.delivered_at }}</span>
                                        </div>
                                    </div>

                                    <!-- Status-specific content -->
                                    <div v-if="card.status === 'delivered'" class="space-y-3">
                                        <a v-if="card.redemption_link"
                                           :href="card.redemption_link"
                                           target="_blank"
                                           class="block w-full text-center px-4 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                                            Redeem Gift Card →
                                        </a>
                                        <p class="text-xs text-gray-600 text-center">
                                            Need help? Email <a href="mailto:support@tremendous.com" class="text-blue-600 hover:text-blue-700">support@tremendous.com</a>
                                        </p>
                                    </div>

                                    <div v-else-if="card.status === 'pending'" class="p-3 bg-yellow-50 border-l-4 border-yellow-400">
                                        <p class="text-sm text-yellow-800">
                                            Your gift card is being processed. You'll receive an email with redemption instructions shortly.
                                        </p>
                                    </div>

                                    <div v-else-if="card.status === 'failed'" class="p-3 bg-red-50 border-l-4 border-red-400">
                                        <p class="text-sm text-red-800 mb-2">
                                            There was an issue processing your gift card.
                                        </p>
                                        <p v-if="card.error_message" class="text-xs text-red-700 font-mono">
                                            {{ card.error_message }}
                                        </p>
                                        <p class="text-xs text-red-700 mt-2">
                                            Our team has been notified and will resolve this shortly.
                                        </p>
                                    </div>

                                    <!-- Related Winner Info -->
                                    <div v-if="card.winner" class="mt-4 pt-4 border-t">
                                        <p class="text-sm text-gray-700">
                                            <strong>Question:</strong> {{ card.winner.daily_question?.question_text || 'N/A' }}
                                        </p>
                                        <Link :href="route('dashboard.winnings')"
                                              class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                            View Full Winnings History →
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="gift_cards.links.length > 3" class="mt-6 flex justify-center gap-2">
                            <Link v-for="link in gift_cards.links" :key="link.label"
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
