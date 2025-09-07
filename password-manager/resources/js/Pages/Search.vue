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

                <!-- Results: Groups -->
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Groups</h3>
                        <div v-if="groups.length === 0 && query" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <p class="text-lg font-medium mb-2">No groups found</p>
                        </div>
                        <div v-else-if="groups.length === 0" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <p class="text-lg font-medium mb-2">Start searching</p>
                            <p>Enter a search term to find your groups and credentials.</p>
                        </div>
                        <div v-else class="grid gap-3">
                            <div
                                v-for="group in groups"
                                :key="group.id"
                                class="bg-gray-50 rounded-lg p-3 hover:bg-gray-100 transition-colors flex items-center justify-between"
                            >
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ group.name }}</h4>
                                        <p v-if="group.description" class="text-xs text-gray-500">{{ group.description }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Link :href="route('groups.show', group.id)" class="text-blue-600 hover:text-blue-800 text-sm">View</Link>
                                    <Link :href="route('groups.edit', group.id)" class="text-gray-600 hover:text-gray-800 text-sm">Edit</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results: Credentials -->
                <div class="bg-white shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Credentials</h3>
                            <Link :href="route('credentials.create')" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Add Credential</Link>
                        </div>

                        <div v-if="credentials.length === 0 && query" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <p class="text-lg font-medium mb-2">No credentials found</p>
                        </div>

                        <div v-else class="grid gap-4">
                            <div
                                v-for="credential in credentials"
                                :key="credential.id"
                                class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ credential.title }}</h4>
                                        <p class="text-sm text-gray-600">{{ credential.username }}</p>
                                        <p v-if="credential.url" class="text-xs text-blue-600 mt-1 truncate">
                                            <a :href="credential.url" target="_blank" rel="noopener noreferrer" class="hover:underline">{{ credential.url }}</a>
                                        </p>
                                        <div v-if="credential.group" class="mt-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ credential.group.name }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <Link :href="route('credentials.edit', credential.id)" class="text-gray-600 hover:text-gray-800 text-sm">Edit</Link>
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
    description?: string;
}

interface Credential {
    id: number;
    title: string;
    username: string;
    url: string;
    group?: Group;
    created_at: string;
    updated_at: string;
}

interface Props {
    credentials: Credential[];
    groups: Group[];
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