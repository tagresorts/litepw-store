<template>
    <Head title="Search Results" />

    <SidebarLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Search Results
                <span v-if="query" class="text-gray-500 font-normal">for "{{ query }}"</span>
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Search Bar -->
                <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center space-x-4">
                            <div class="flex-1 relative">
                                <input
                                    v-model="searchQuery"
                                    @keyup.enter="performSearch"
                                    type="text"
                                    placeholder="Search credentials..."
                                    class="w-full px-4 py-3 pl-12 pr-4 text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                            <button
                                @click="performSearch"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                Search
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results -->
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium">
                                {{ credentials.length }} result{{ credentials.length !== 1 ? 's' : '' }} found
                            </h3>
                            <Link
                                :href="route('credentials.create')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Credential
                            </Link>
                        </div>

                        <div v-if="credentials.length === 0 && query" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <p class="text-lg font-medium mb-2">No credentials found</p>
                            <p class="mb-4">Try searching with different keywords or create a new credential.</p>
                            <Link
                                :href="route('credentials.create')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                Add New Credential
                            </Link>
                        </div>

                        <div v-else-if="credentials.length === 0" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <p class="text-lg font-medium mb-2">Start searching</p>
                            <p>Enter a search term to find your credentials.</p>
                        </div>

                        <div v-else class="grid gap-4">
                            <div
                                v-for="credential in credentials"
                                :key="credential.id"
                                class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ credential.name }}</h4>
                                        <p class="text-sm text-gray-600">{{ credential.username }}</p>
                                        <p v-if="credential.url" class="text-xs text-gray-500 mt-1">{{ credential.url }}</p>
                                        <div v-if="credential.group" class="mt-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                                {{ credential.group.name }}
                                            </span>
                                        </div>
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
    </SidebarLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';

interface Group {
    id: number;
    name: string;
}

interface Credential {
    id: number;
    name: string;
    username: string;
    url: string;
    group?: Group;
    created_at: string;
    updated_at: string;
}

interface Props {
    credentials: Credential[];
    query?: string;
}

const props = defineProps<Props>();

const searchQuery = ref(props.query || '');

onMounted(() => {
    // Focus search input when component mounts
    const searchInput = document.querySelector('input[type="text"]') as HTMLInputElement;
    if (searchInput) {
        searchInput.focus();
    }
});

const performSearch = () => {
    if (searchQuery.value.trim()) {
        router.visit(route('search', { q: searchQuery.value.trim() }));
    }
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