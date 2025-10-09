<script setup>
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
</script>

<template>
    <GuestLayout>
        <Head title="Privacy Policy" />

        <div class="max-w-4xl mx-auto px-4 py-12">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Privacy Policy</h1>
                <p class="text-sm text-gray-600 mb-8">Last Updated: {{ new Date().toLocaleDateString() }}</p>

                <div class="prose prose-blue max-w-none space-y-6">
                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">1. Introduction</h2>
                        <p class="text-gray-700 leading-relaxed">
                            This Privacy Policy describes how the Golden Question Contest ("we", "us", or "our") collects, uses, and shares your personal information when you use our Service. We are committed to protecting your privacy and ensuring transparency in our data practices.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">2. Information We Collect</h2>

                        <h3 class="text-xl font-semibold text-gray-800 mt-4 mb-2">2.1 Information You Provide</h3>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li><strong>Account Information:</strong> Name, email address, birthdate</li>
                            <li><strong>Contest Submissions:</strong> Answers to trivia questions, submission timestamps</li>
                            <li><strong>Communications:</strong> Messages you send to us for support or inquiries</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-gray-800 mt-4 mb-2">2.2 Automatically Collected Information</h3>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li><strong>Device Information:</strong> IP address, browser type, operating system</li>
                            <li><strong>Usage Data:</strong> Pages visited, time spent, clickstream data</li>
                            <li><strong>Location Data:</strong> Approximate location via IP address, GPS coordinates when scanning QR codes (if permitted)</li>
                            <li><strong>Device Fingerprint:</strong> Unique identifier to prevent fraud and abuse</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-gray-800 mt-4 mb-2">2.3 Third-Party Data</h3>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li><strong>reCAPTCHA:</strong> Google reCAPTCHA collects device and behavioral data to prevent automated abuse</li>
                            <li><strong>Gift Card Provider:</strong> Tremendous.com processes gift card deliveries and may collect additional information</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">3. How We Use Your Information</h2>
                        <p class="text-gray-700 leading-relaxed mb-3">We use the collected information for:</p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li><strong>Contest Administration:</strong> Verifying eligibility, processing submissions, determining winners</li>
                            <li><strong>Prize Fulfillment:</strong> Delivering gift cards to winners via email</li>
                            <li><strong>Fraud Prevention:</strong> Detecting and preventing cheating, bot usage, and account abuse</li>
                            <li><strong>Service Improvement:</strong> Analyzing usage patterns, fixing bugs, improving features</li>
                            <li><strong>Communications:</strong> Sending winner notifications, service updates, and support responses</li>
                            <li><strong>Legal Compliance:</strong> Meeting legal obligations and enforcing our Terms of Service</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">4. Information Sharing and Disclosure</h2>
                        <p class="text-gray-700 leading-relaxed mb-3">We share your information with:</p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li><strong>Gift Card Provider:</strong> Tremendous.com receives your name and email to deliver prizes</li>
                            <li><strong>Google reCAPTCHA:</strong> Collects device data for bot detection (see Google's Privacy Policy)</li>
                            <li><strong>Service Providers:</strong> Hosting, analytics, and infrastructure providers under confidentiality agreements</li>
                            <li><strong>Legal Requirements:</strong> When required by law, court order, or to protect our rights</li>
                            <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or asset sale</li>
                        </ul>
                        <p class="text-gray-700 leading-relaxed mt-3">
                            <strong>We do not sell your personal information to third parties.</strong>
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">5. Data Retention</h2>
                        <p class="text-gray-700 leading-relaxed">
                            We retain your personal information for as long as your account is active or as needed to provide services. After account termination, we may retain certain information for legal compliance, fraud prevention, and record-keeping purposes. You may request account deletion at any time.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">6. Security</h2>
                        <p class="text-gray-700 leading-relaxed">
                            We implement reasonable security measures to protect your information, including:
                        </p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2 mt-3">
                            <li>Encrypted data transmission (HTTPS/SSL)</li>
                            <li>Secure password hashing (bcrypt)</li>
                            <li>Regular security assessments and updates</li>
                            <li>Access controls and authentication</li>
                        </ul>
                        <p class="text-gray-700 leading-relaxed mt-3">
                            However, no system is 100% secure. We cannot guarantee absolute security of your information.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">7. Your Rights and Choices</h2>
                        <p class="text-gray-700 leading-relaxed mb-3">You have the right to:</p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li><strong>Access:</strong> Request a copy of your personal data</li>
                            <li><strong>Correction:</strong> Update or correct inaccurate information</li>
                            <li><strong>Deletion:</strong> Request deletion of your account and data</li>
                            <li><strong>Opt-Out:</strong> Unsubscribe from marketing emails (we send very few)</li>
                            <li><strong>Data Portability:</strong> Receive your data in a structured, machine-readable format</li>
                        </ul>
                        <p class="text-gray-700 leading-relaxed mt-3">
                            To exercise these rights, please contact us using the information provided below.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">8. Cookies and Tracking Technologies</h2>
                        <p class="text-gray-700 leading-relaxed">
                            We use cookies and similar technologies to maintain your session, remember preferences, and analyze usage. You can control cookies through your browser settings, but disabling them may affect Service functionality.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">9. Children's Privacy</h2>
                        <p class="text-gray-700 leading-relaxed">
                            Our Service is not intended for individuals under 18. We do not knowingly collect information from children. If we discover we have collected information from a child, we will delete it immediately.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">10. International Data Transfers</h2>
                        <p class="text-gray-700 leading-relaxed">
                            Your information may be transferred to and processed in countries other than your own. We ensure appropriate safeguards are in place for such transfers.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">11. Changes to This Privacy Policy</h2>
                        <p class="text-gray-700 leading-relaxed">
                            We may update this Privacy Policy from time to time. We will notify you of significant changes by posting the new policy on this page and updating the "Last Updated" date. Your continued use of the Service constitutes acceptance of the updated policy.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">12. Contact Us</h2>
                        <p class="text-gray-700 leading-relaxed">
                            For questions, concerns, or requests regarding this Privacy Policy or your personal information, please contact us at the email address provided in the contest materials.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-3">13. Third-Party Services</h2>
                        <p class="text-gray-700 leading-relaxed mb-3">
                            This Service integrates with third-party providers who have their own privacy policies:
                        </p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li><strong>Google reCAPTCHA:</strong> <a href="https://policies.google.com/privacy" target="_blank" class="text-blue-600 hover:underline">Google Privacy Policy</a></li>
                            <li><strong>Tremendous:</strong> <a href="https://www.tremendous.com/privacy" target="_blank" class="text-blue-600 hover:underline">Tremendous Privacy Policy</a></li>
                        </ul>
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
