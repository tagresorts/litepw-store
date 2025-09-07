<template>
    <Head title="Add Credential" />

    <SidebarLayout>
        <template #header>
            Add New Credential
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">
                                    Title *
                                </label>
                                <input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': form.errors.title }"
                                    required
                                />
                                <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.title }}
                                </p>
                            </div>

                            <!-- Multiple Username/Password Entries -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Username/Password Entries *
                                    </label>
                                    <button
                                        type="button"
                                        @click="addCredentialEntry"
                                        :disabled="credentialEntries.length >= 6"
                                        class="inline-flex items-center px-3 py-1 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        {{ credentialEntries.length >= 6 ? 'Max Entries (6)' : 'Add Entry' }}
                                    </button>
                                </div>
                                
                                <CredentialEntries
                                    :entries="credentialEntries"
                                    @remove-entry="removeCredentialEntry"
                                    @generate-password="generatePasswordForEntry"
                                    @add-url="addUrlToEntry"
                                    @remove-url="removeUrlFromEntry"
                                />
                                
                                <p v-if="form.errors.credential_entries" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.credential_entries }}
                                </p>
                            </div>

                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">
                                    Notes
                                </label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': form.errors.notes }"
                                ></textarea>
                                <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.notes }}
                                </p>
                            </div>

                            <div>
                                <label for="group_id" class="block text-sm font-medium text-gray-700">
                                    Group
                                </label>
                                <select
                                    id="group_id"
                                    v-model="form.group_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Select a group (optional)</option>
                                    <option v-for="group in groups" :key="group.id" :value="group.id">
                                        {{ group.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.group_id" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.group_id }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between pt-6 border-t">
                                <Link
                                    :href="route('credentials.index')"
                                    class="text-gray-600 hover:text-gray-800"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
                                >
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ form.processing ? 'Saving...' : 'Save Credential' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import CredentialEntries from '@/Components/Credentials/CredentialEntries.vue';

interface Group {
    id: number;
    name: string;
}

interface CredentialEntry {
    id: number;
    username: string;
    password: string;
    showPassword: boolean;
    urls: Array<{ value: string }>;
}

interface Props {
    groups: Group[];
    navigationTree: any[];
}

const props = defineProps<Props>();

let entryIdCounter = 1;

const credentialEntries = reactive<CredentialEntry[]>([
    {
        id: entryIdCounter++,
        username: '',
        password: '',
        showPassword: false,
        urls: [{ value: '' }]
    }
]);

const form = useForm({
    title: '',
    credential_entries: [] as any[],
    notes: '',
    group_id: '',
});

const addCredentialEntry = () => {
    if (credentialEntries.length < 6) {
        credentialEntries.push({
            id: entryIdCounter++,
            username: '',
            password: '',
            showPassword: false,
            urls: [{ value: '' }]
        });
    }
};

const removeCredentialEntry = (index: number) => {
    if (credentialEntries.length > 1) {
        credentialEntries.splice(index, 1);
    }
};

const addUrlToEntry = (entryIndex: number) => {
    credentialEntries[entryIndex].urls.push({ value: '' });
};

const removeUrlFromEntry = (entryIndex: number, urlIndex: number) => {
    if (credentialEntries[entryIndex].urls.length > 1) {
        credentialEntries[entryIndex].urls.splice(urlIndex, 1);
    }
};

const generatePasswordForEntry = async (entryIndex: number) => {
    try {
        const response = await fetch(route('credentials.generate-password'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
        });
        
        if (response.ok) {
            const data = await response.json();
            credentialEntries[entryIndex].password = data.password;
        }
    } catch (error) {
        console.error('Failed to generate password:', error);
        // Fallback to local generation
        const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
        let password = '';
        for (let i = 0; i < 16; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        credentialEntries[entryIndex].password = password;
    }
};

const submit = () => {
    // Prepare credential entries for submission
    form.credential_entries = credentialEntries.map(entry => ({
        username: entry.username,
        password: entry.password,
        urls: entry.urls.filter(url => url.value.trim()).map(url => url.value.trim())
    }));

    form.post(route('credentials.store'));
};
</script>