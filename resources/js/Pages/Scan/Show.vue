<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    sticker: Object,
    scans_count: Number,
    recent_scans: Array,
});
</script>

<template>
    <Head title="Sticker Details" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Sticker Details
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Sticker Information -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sticker Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-600">Unique Code</p>
                                <p class="text-lg font-mono font-bold text-gray-900">{{ sticker.unique_code }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Location Name</p>
                                <p class="text-lg font-semibold text-gray-900">{{ sticker.location_name || 'Not set' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Property Name</p>
                                <p class="text-lg font-semibold text-gray-900">{{ sticker.property_name || 'Not set' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <span :class="{
                                    'inline-block px-3 py-1 rounded-full text-sm font-semibold': true,
                                    'bg-green-100 text-green-800': sticker.status === 'active',
                                    'bg-red-100 text-red-800': sticker.status === 'inactive'
                                }">
                                    {{ sticker.status }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Scans</p>
                                <p class="text-2xl font-bold text-blue-600">{{ scans_count }}</p>
                            </div>
                            <div v-if="sticker.installed_at">
                                <p class="text-sm text-gray-600">Installed Date</p>
                                <p class="text-lg font-semibold text-gray-900">{{ sticker.installed_at }}</p>
                            </div>
                        </div>

                        <!-- Location Coordinates -->
                        <div v-if="sticker.latitude && sticker.longitude" class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">GPS Coordinates</p>
                            <p class="font-mono text-sm text-gray-900">
                                {{ sticker.latitude }}, {{ sticker.longitude }}
                            </p>
                        </div>

                        <!-- QR Code URL -->
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">QR Code URL</p>
                            <p class="font-mono text-sm text-gray-900 break-all">
                                {{ route('scan', { code: sticker.unique_code }) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Recent Scans -->
                <div v-if="recent_scans.length > 0" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Scans (Last 10)</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            User
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            IP Address
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Location
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Scanned At
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="scan in recent_scans" :key="scan.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ scan.user?.name || 'Anonymous' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">
                                            {{ scan.ip_address || 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span v-if="scan.scan_latitude && scan.scan_longitude">
                                                {{ scan.scan_latitude.toFixed(4) }}, {{ scan.scan_longitude.toFixed(4) }}
                                            </span>
                                            <span v-else>N/A</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ scan.scanned_at }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- No Scans Message -->
                <div v-else class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-gray-500">No scans recorded yet for this sticker.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
