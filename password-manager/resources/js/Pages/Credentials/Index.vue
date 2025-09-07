<template>
    <Head title="Credentials" />

    <SidebarLayout>
        <template #header>
            Credentials
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

                        <div v-if="credentials.length === 0" class="text-center py-8 text-gray-500">
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
                            <CredentialCard
                                v-for="credential in credentials"
                                :key="credential.id"
                                :credential="credential"
                            />
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import CredentialCard from '@/Components/Credentials/CredentialCard.vue';

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
    created_at: string;
    updated_at: string;
}

interface Props {
    credentials: Credential[];
}

defineProps<Props>();

</script>