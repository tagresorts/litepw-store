<template>
    <Head title="Credentials" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Credentials
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium">Manage Credentials</h3>
                            <Link
                                :href="route('credentials.create')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Credential
                            </Link>
                        </div>

                        <div v-if="credentials.data.length === 0" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z" />
                            </svg>
                            <p class="text-lg font-medium mb-2">No credentials found</p>
                            <p class="mb-4">Get started by adding your first credential.</p>
                            <Link
                                :href="route('credentials.create')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                Add Your First Credential
                            </Link>
                        </div>

                        <div v-else class="grid gap-4">
                            <div
                                v-for="credential in credentials.data"
                                :key="credential.id"
                                class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ credential.name }}</h4>
                                        <p class="text-sm text-gray-600">{{ credential.username }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ credential.url }}</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button
                                            @click="copyPassword(credential.id)"
                                            class="p-2 text-gray-400 hover:text-gray-600 rounded-md hover:bg-white"
                                            title="Copy Password"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                        <Link
                                            :href="route('credentials.edit', credential.id)"
                                            class="p-2 text-gray-400 hover:text-gray-600 rounded-md hover:bg-white"
                                            title="Edit"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </Link>
                                        <button
                                            @click="deleteCredential(credential.id)"
                                            class="p-2 text-red-400 hover:text-red-600 rounded-md hover:bg-white"
                                            title="Delete"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="credentials.links && credentials.links.length > 3" class="mt-6">
                            <nav class="flex justify-center">
                                <div class="flex space-x-1">
                                    <Link
                                        v-for="link in credentials.links"
                                        :key="link.label"
                                        :href="link.url || '#'"
                                        :class="[
                                            'px-3 py-2 text-sm font-medium rounded-md',
                                            link.active
                                                ? 'bg-blue-600 text-white'
                                                : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'
                                        ]"
                                        v-html="link.label"
                                    />
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

interface Credential {
    id: number;
    name: string;
    username: string;
    url: string;
    created_at: string;
    updated_at: string;
}

interface PaginatedCredentials {
    data: Credential[];
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
    meta: {
        current_page: number;
        total: number;
        per_page: number;
    };
}

interface Props {
    credentials: PaginatedCredentials;
}

defineProps<Props>();

const copyPassword = async (credentialId: number) => {
    try {
        const response = await fetch(route('credentials.password', credentialId), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        });
        
        if (response.ok) {
            const data = await response.json();
            await navigator.clipboard.writeText(data.password);
            // You could add a toast notification here
            console.log('Password copied to clipboard');
        }
    } catch (error) {
        console.error('Failed to copy password:', error);
    }
};

const deleteCredential = (credentialId: number) => {
    if (confirm('Are you sure you want to delete this credential?')) {
        router.delete(route('credentials.destroy', credentialId));
    }
};
</script>