<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    birthdate: '',
    password: '',
    password_confirmation: '',
    // Honeypot fields
    website: '',
    phone: '',
    company: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Join the Fun!</h1>
            <p class="text-gray-600">Create an account to start playing and winning</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="name" value="Name" class="font-semibold text-gray-700" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 focus:border-yellow-500 focus:ring-yellow-500"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Your full name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" class="font-semibold text-gray-700" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 focus:border-yellow-500 focus:ring-yellow-500"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="your@email.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="birthdate" value="Birthdate (Must be 18+)" class="font-semibold text-gray-700" />

                <TextInput
                    id="birthdate"
                    type="date"
                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 focus:border-yellow-500 focus:ring-yellow-500"
                    v-model="form.birthdate"
                    required
                    autocomplete="bday"
                />

                <InputError class="mt-2" :message="form.errors.birthdate" />
                <p class="mt-1 text-xs text-gray-600 italic">You must be 18 years or older to participate</p>
            </div>

            <div>
                <InputLabel for="password" value="Password" class="font-semibold text-gray-700" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 focus:border-yellow-500 focus:ring-yellow-500"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Confirm Password" class="font-semibold text-gray-700" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 focus:border-yellow-500 focus:ring-yellow-500"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <!-- Honeypot fields - hidden from users, visible to bots -->
            <div style="position: absolute; left: -5000px;" aria-hidden="true">
                <input type="text" v-model="form.website" name="website" tabindex="-1" autocomplete="off" placeholder="Your website">
                <input type="tel" v-model="form.phone" name="phone" tabindex="-1" autocomplete="off" placeholder="Phone number">
                <input type="text" v-model="form.company" name="company" tabindex="-1" autocomplete="off" placeholder="Company name">
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-bold text-lg rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Creating Account...' : 'Sign Up' }}
                </button>
            </div>

            <div class="text-center pt-4 border-t-2 border-gray-200">
                <p class="text-gray-700">
                    Already have an account?
                    <Link :href="route('login')" class="font-bold text-blue-600 hover:text-blue-800 underline">
                        Log in here
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
