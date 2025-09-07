<template>
    <Head title="View Credential" />

    <SidebarLayout>
        <template #header>
            View Credential
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ credential.title }}</h1>
                                <p v-if="credential.group" class="text-sm text-gray-600 mt-1">
                                    Group: {{ credential.group.name }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button
                                    @click="toggleFavorite"
                                    :class="credential.is_favorite ? 'text-yellow-500' : 'text-gray-400'"
                                    class="hover:text-yellow-600 transition-colors"
                                    title="Toggle Favorite"
                                >
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                    </svg>
                                </button>
                                <Link
                                    :href="route('credentials.edit', credential.id)"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </Link>
                            </div>
                        </div>

                        <!-- Credential Entries -->
                        <div v-if="credential.credential_entries && credential.credential_entries.length > 0" class="space-y-6">
                            <div
                                v-for="(entry, index) in credential.credential_entries"
                                :key="entry.id"
                                class="border border-gray-200 rounded-lg p-6 bg-gray-50"
                            >
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Entry {{ index + 1 }}</h3>
                                    <button
                                        @click="copyPassword(entry.password)"
                                        class="inline-flex items-center px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        Copy Password
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Username -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Username
                                        </label>
                                        <div class="flex items-center space-x-2">
                                            <input
                                                :value="entry.username"
                                                readonly
                                                class="flex-1 rounded-md border-gray-300 bg-gray-100 text-gray-900"
                                            />
                                            <button
                                                @click="copyToClipboard(entry.username)"
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Password
                                        </label>
                                        <div class="flex items-center space-x-2">
                                            <input
                                                :value="entry.password"
                                                :type="showPasswords[index] ? 'text' : 'password'"
                                                readonly
                                                class="flex-1 rounded-md border-gray-300 bg-gray-100 text-gray-900"
                                            />
                                            <button
                                                @click="togglePasswordVisibility(index)"
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50"
                                            >
                                                <svg v-if="showPasswords[index]" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                                </svg>
                                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- URLs -->
                                <div v-if="entry.urls && entry.urls.length > 0" class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        URLs/IP Addresses
                                    </label>
                                    <div class="space-y-2">
                                        <div
                                            v-for="(url, urlIndex) in entry.urls"
                                            :key="urlIndex"
                                            class="flex items-center space-x-2"
                                        >
                                            <input
                                                :value="url"
                                                readonly
                                                class="flex-1 rounded-md border-gray-300 bg-gray-100 text-gray-900"
                                            />
                                            <button
                                                @click="copyToClipboard(url)"
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fallback for old format -->
                        <div v-else class="border border-gray-200 rounded-lg p-6 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Username
                                    </label>
                                    <div class="flex items-center space-x-2">
                                        <input
                                            :value="credential.username"
                                            readonly
                                            class="flex-1 rounded-md border-gray-300 bg-gray-100 text-gray-900"
                                        />
                                        <button
                                            @click="copyToClipboard(credential.username)"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Password
                                    </label>
                                    <div class="flex items-center space-x-2">
                                        <input
                                            :value="credential.password"
                                            :type="showPassword ? 'text' : 'password'"
                                            readonly
                                            class="flex-1 rounded-md border-gray-300 bg-gray-100 text-gray-900"
                                        />
                                        <button
                                            @click="showPassword = !showPassword"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50"
                                        >
                                            <svg v-if="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                            </svg>
                                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div v-if="credential.url" class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    URL
                                </label>
                                <div class="flex items-center space-x-2">
                                    <input
                                        :value="credential.url"
                                        readonly
                                        class="flex-1 rounded-md border-gray-300 bg-gray-100 text-gray-900"
                                    />
                                    <button
                                        @click="copyToClipboard(credential.url)"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div v-if="credential.notes" class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Notes
                            </label>
                            <div class="rounded-md border border-gray-300 bg-gray-50 p-4">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ credential.notes }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-6 border-t mt-6">
                            <Link
                                :href="route('credentials.index')"
                                class="text-gray-600 hover:text-gray-800"
                            >
                                ← Back to Credentials
                            </Link>
                            <div class="flex items-center space-x-3">
                                <button
                                    @click="deleteCredential"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';

interface CredentialEntry {
    id: number;
    username: string;
    password: string;
    urls: string[];
}

interface Credential {
    id: number;
    title: string;
    username: string;
    password: string;
    url: string;
    notes: string;
    is_favorite: boolean;
    credential_entries: CredentialEntry[];
    group?: {
        name: string;
    };
}

interface Props {
    credential: Credential;
}

const props = defineProps<Props>();

const showPassword = ref(false);
const showPasswords = reactive<boolean[]>([]);

// Initialize password visibility array
if (props.credential.credential_entries) {
    props.credential.credential_entries.forEach(() => {
        showPasswords.push(false);
    });
}

const togglePasswordVisibility = (index: number) => {
    showPasswords[index] = !showPasswords[index];
};

const copyToClipboard = async (text: string) => {
    try {
        await navigator.clipboard.writeText(text);
        // You could add a toast notification here
        console.log('Copied to clipboard:', text);
    } catch (error) {
        console.error('Failed to copy to clipboard:', error);
    }
};

const copyPassword = async (password: string) => {
    await copyToClipboard(password);
};

const toggleFavorite = () => {
    router.post(route('credentials.toggle-favorite', props.credential.id), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const deleteCredential = () => {
    if (confirm('Are you sure you want to delete this credential?')) {
        router.delete(route('credentials.destroy', props.credential.id));
    }
};
</script>