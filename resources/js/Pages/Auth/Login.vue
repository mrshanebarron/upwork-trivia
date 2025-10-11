<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back!</h1>
            <p class="text-gray-600">Log in to play and track your winnings</p>
        </div>

        <div v-if="status" class="mb-4 p-3 bg-green-100 border-l-4 border-green-500 text-green-700 text-sm">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Email" class="font-semibold text-gray-700" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 focus:border-yellow-500 focus:ring-yellow-500"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="your@email.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" class="font-semibold text-gray-700" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 focus:border-yellow-500 focus:ring-yellow-500"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-700 font-medium">Remember me</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium underline"
                >
                    Forgot password?
                </Link>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-bold text-lg rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Logging in...' : 'Log In' }}
                </button>
            </div>

            <div class="text-center pt-4 border-t-2 border-gray-200">
                <p class="text-gray-700">
                    Don't have an account?
                    <Link :href="route('register')" class="font-bold text-blue-600 hover:text-blue-800 underline">
                        Sign up here
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
