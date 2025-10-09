<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    winners: Object, // Paginated collection
    total_winnings: Number,
});
</script>

<template>
    <Head title="My Winnings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                My Winnings
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Total Winnings Card -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-gradient-to-r from-green-500 to-green-600">
                        <h3 class="text-lg font-semibold text-white mb-2">Total Winnings</h3>
                        <p class="text-4xl font-bold text-white">${{ total_winnings }}</p>
                    </div>
                </div>

                <!-- Winnings List -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Winning History</h3>

                        <div v-if="winners.data.length === 0" class="text-center py-8">
                            <p class="text-gray-600">No winnings yet. Keep playing!</p>
                            <Link :href="route('contest.show')"
                                  class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Play Golden Question
                            </Link>
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="winner in winners.data" :key="winner.id"
                                 class="border rounded-lg p-4 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-2xl font-bold text-green-600">${{ winner.prize_amount }}</span>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                                  :class="{
                                                      'bg-green-100 text-green-800': winner.gift_card?.status === 'delivered',
                                                      'bg-yellow-100 text-yellow-800': winner.gift_card?.status === 'pending',
                                                      'bg-red-100 text-red-800': winner.gift_card?.status === 'failed'
                                                  }">
                                                {{ winner.gift_card?.status || 'Processing' }}
                                            </span>
                                        </div>
                                        <p class="text-gray-900 font-medium mb-1">
                                            {{ winner.daily_question.question_text }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Won on {{ winner.created_at }}
                                        </p>
                                    </div>
                                    <Link v-if="winner.gift_card?.redemption_link"
                                          :href="winner.gift_card.redemption_link"
                                          target="_blank"
                                          class="ml-4 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition whitespace-nowrap">
                                        Redeem Card
                                    </Link>
                                </div>

                                <!-- Gift Card Details -->
                                <div v-if="winner.gift_card" class="mt-3 p-3 bg-gray-50 rounded">
                                    <p class="text-sm text-gray-700">
                                        <strong>Order ID:</strong> {{ winner.gift_card.order_id }}
                                    </p>
                                    <p class="text-sm text-gray-700">
                                        <strong>Provider:</strong> {{ winner.gift_card.provider }}
                                    </p>
                                    <p v-if="winner.gift_card.delivered_at" class="text-sm text-gray-700">
                                        <strong>Delivered:</strong> {{ winner.gift_card.delivered_at }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="winners.links.length > 3" class="mt-6 flex justify-center gap-2">
                            <Link v-for="link in winners.links" :key="link.label"
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

                <!-- Help Section -->
                <div class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Need Help?</h3>
                        <p class="text-gray-700 mb-2">
                            For issues with gift card redemption, please contact Tremendous support:
                        </p>
                        <a href="mailto:support@tremendous.com"
                           class="text-blue-600 hover:text-blue-700 font-medium">
                            support@tremendous.com
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
