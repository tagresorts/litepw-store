<template>
    <Head :title="group.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link
                        :href="route('groups.index')"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ group.name }}
                    </h2>
                </div>
                <div class="flex items-center space-x-2">
                    <Link
                        :href="route('groups.edit', group.id)"
                        class="inline-flex items-center px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
                    >
                        Edit Group
                    </Link>
                    <Link
                        :href="route('credentials.create', { group_id: group.id })"
                        class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    >
                        Add Credential
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Group Info -->
                <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center space-x-3 mb-4">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ group.name }}</h3>
                                <p v-if="group.description" class="text-gray-600">{{ group.description }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-6 text-sm text-gray-500">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z" />
                                </svg>
                                {{ credentials.length }} credentials
                            </span>
                            <span>Level {{ group.level }}</span>
                            <span>Created {{ formatDate(group.created_at) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Credentials -->
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-lg font-medium">Credentials in this Group</h4>
                            <Link
                                :href="route('credentials.create', { group_id: group.id })"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Credential
                            </Link>
                        </div>

                        <div v-if="credentials.length === 0" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z" />
                            </svg>
                            <p class="text-lg font-medium mb-2">No credentials in this group</p>
                            <p class="mb-4">Add your first credential to this group.</p>
                            <Link
                                :href="route('credentials.create', { group_id: group.id })"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                Add First Credential
                            </Link>
                        </div>

                        <div v-else class="grid gap-4">
                            <div
                                v-for="credential in credentials"
                                :key="credential.id"
                                class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h5 class="font-medium text-gray-900">{{ credential.name }}</h5>
                                        <p class="text-sm text-gray-600">{{ credential.username }}</p>
                                        <p v-if="credential.url" class="text-xs text-gray-500 mt-1">{{ credential.url }}</p>
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
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

interface Group {
    id: number;
    name: string;
    description: string;
    level: number;
    created_at: string;
    updated_at: string;
}

interface Credential {
    id: number;
    name: string;
    username: string;
    url: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    group: Group;
    credentials: Credential[];
}

defineProps<Props>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString();
};

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