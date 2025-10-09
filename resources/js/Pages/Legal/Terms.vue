<script setup>
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
</script>

<template>
    <GuestLayout>
        <Head title="Terms of Service" />

        <div class="max-w-4xl mx-auto px-4 py-12">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Terms of Service</h1>
                <p class="text-sm text-gray-600 mb-8">Last Updated: {{ new Date().toLocaleDateString() }}</p>

                <div class="prose prose-blue max-w-none space-y-6">
                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">1. Acceptance of Terms</h2>
                        <p class="text-gray-700 leading-relaxed">
                            By accessing and using the Golden Question Contest ("Service"), you accept and agree to be bound by the terms and provisions of this agreement. If you do not agree to these terms, please do not use this Service.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">2. Eligibility</h2>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li>You must be at least 18 years old to participate in the contest.</li>
                            <li>You must provide accurate birth date information during registration.</li>
                            <li>Only one account per person is permitted.</li>
                            <li>Employees, contractors, and immediate family members of the contest organizer are not eligible to win prizes.</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">3. Contest Rules</h2>
                        <p class="text-gray-700 leading-relaxed mb-3">
                            <strong>Daily Golden Question Contest:</strong>
                        </p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li><strong>One Question Per Day:</strong> A new trivia question is posted daily at a random time.</li>
                            <li><strong>First Correct Answer Wins:</strong> The first user to submit the correct answer wins the daily prize.</li>
                            <li><strong>Prize Amount:</strong> Winners receive a $10 gift card delivered via email.</li>
                            <li><strong>Win Cooldown:</strong> Users who win a prize must wait 30 days before being eligible to win again.</li>
                            <li><strong>One Submission Per Day:</strong> Each user may submit only one answer per question per day.</li>
                            <li><strong>Anti-Cheating:</strong> Automated submissions, bot usage, or multiple accounts will result in immediate disqualification and account termination.</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">4. Prize Distribution</h2>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li>Gift cards are delivered electronically via email through our third-party provider (Tremendous).</li>
                            <li>Winners will receive redemption instructions within 24-48 hours of winning.</li>
                            <li>Prizes have no cash value and are non-transferable.</li>
                            <li>We are not responsible for gift card delivery issues beyond our control.</li>
                            <li>For gift card support, contact the provider directly using the link in your redemption email.</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">5. User Conduct</h2>
                        <p class="text-gray-700 leading-relaxed mb-3">You agree not to:</p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li>Use automated scripts, bots, or tools to submit answers</li>
                            <li>Create multiple accounts to circumvent contest rules</li>
                            <li>Share answers or coordinate with other users</li>
                            <li>Attempt to manipulate or interfere with the contest system</li>
                            <li>Violate any applicable laws or regulations</li>
                            <li>Use offensive, inappropriate, or malicious content</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">6. Account Termination</h2>
                        <p class="text-gray-700 leading-relaxed">
                            We reserve the right to suspend or terminate your account at any time for violation of these terms, suspicious activity, or any other reason at our sole discretion. Upon termination, you forfeit any pending prizes and lose access to the Service.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">7. Modifications to Service</h2>
                        <p class="text-gray-700 leading-relaxed">
                            We reserve the right to modify, suspend, or discontinue the Service at any time without notice. We may also modify contest rules, prize amounts, and eligibility requirements at our discretion. Continued use of the Service constitutes acceptance of any modifications.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">8. Intellectual Property</h2>
                        <p class="text-gray-700 leading-relaxed">
                            All content, including but not limited to text, graphics, logos, images, and software, is the property of the Service or its licensors and is protected by copyright and trademark laws. You may not reproduce, distribute, or create derivative works without express written permission.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">9. Limitation of Liability</h2>
                        <p class="text-gray-700 leading-relaxed">
                            TO THE MAXIMUM EXTENT PERMITTED BY LAW, WE SHALL NOT BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, OR ANY LOSS OF PROFITS OR REVENUES, WHETHER INCURRED DIRECTLY OR INDIRECTLY, OR ANY LOSS OF DATA, USE, GOODWILL, OR OTHER INTANGIBLE LOSSES.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">10. Disclaimer of Warranties</h2>
                        <p class="text-gray-700 leading-relaxed">
                            THE SERVICE IS PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, OR NON-INFRINGEMENT.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">11. Governing Law</h2>
                        <p class="text-gray-700 leading-relaxed">
                            These Terms shall be governed by and construed in accordance with the laws of the jurisdiction in which the Service operates, without regard to its conflict of law provisions.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">12. Contact Information</h2>
                        <p class="text-gray-700 leading-relaxed">
                            For questions about these Terms of Service, please contact us at the email address provided in the contest materials.
                        </p>
                    </section>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <Link :href="route('home')" class="text-blue-600 hover:text-blue-700 font-medium">
                        ← Back to Home
                    </Link>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
