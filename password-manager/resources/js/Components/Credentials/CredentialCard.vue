<template>
    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
        <div class="flex items-center justify-between mb-3">
            <div class="flex-1">
                <h4 class="font-medium text-gray-900">{{ credential.title }}</h4>
                <p class="text-xs text-gray-500 mt-1">
                    {{ credential.group?.name || 'No Group' }} • 
                    {{ credential.entry_count }} {{ credential.entry_count === 1 ? 'entry' : 'entries' }}
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <button
                    @click="toggleFavorite"
                    :class="credential.is_favorite ? 'text-yellow-500' : 'text-gray-400'"
                    class="hover:text-yellow-600 transition-colors"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                </button>
                <Link
                    :href="route('credentials.show', credential.id)"
                    class="text-blue-600 hover:text-blue-800"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </Link>
                <Link
                    :href="route('credentials.edit', credential.id)"
                    class="text-green-600 hover:text-green-800"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </Link>
                <button
                    @click="deleteCredential"
                    class="text-red-600 hover:text-red-800"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Dynamic Credential Entries -->
        <div v-if="credential.credential_entries && credential.credential_entries.length > 0" class="space-y-2">
            <div
                v-for="(entry, index) in credential.credential_entries"
                :key="entry.id"
                class="bg-white rounded border p-3 text-sm"
            >
                <div class="flex items-center justify-between mb-2">
                    <span class="font-medium text-gray-700">Entry {{ index + 1 }}</span>
                    <span class="text-xs text-gray-500">{{ entry.username }}</span>
                </div>
                
                <div v-if="entry.urls && entry.urls.length > 0" class="text-xs text-gray-600">
                    <div v-for="url in entry.urls" :key="url" class="truncate">
                        {{ url }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Fallback for old format -->
        <div v-else class="text-sm text-gray-600">
            <p>{{ credential.username }}</p>
            <p v-if="credential.url" class="text-xs text-gray-500 truncate">{{ credential.url }}</p>
        </div>

        <div v-if="credential.notes" class="mt-2 text-xs text-gray-500 italic">
            {{ credential.notes }}
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';

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
    url: string;
    notes: string;
    is_favorite: boolean;
    entry_count: number;
    credential_entries: CredentialEntry[];
    group?: {
        name: string;
    };
}

interface Props {
    credential: Credential;
}

const props = defineProps<Props>();

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