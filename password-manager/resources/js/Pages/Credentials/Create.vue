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
                                        class="inline-flex items-center px-3 py-1 text-sm bg-green-600 text-white rounded-md hover:bg-green-700"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Entry
                                    </button>
                                </div>
                                
                                <div class="space-y-4">
                                    <div
                                        v-for="(entry, index) in credentialEntries"
                                        :key="entry.id"
                                        class="p-4 border border-gray-200 rounded-lg bg-gray-50"
                                    >
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="text-sm font-medium text-gray-700">Entry {{ index + 1 }}</h4>
                                            <button
                                                v-if="credentialEntries.length > 1"
                                                type="button"
                                                @click="removeCredentialEntry(index)"
                                                class="text-red-500 hover:text-red-700"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Username -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                                    Username/Email
                                                </label>
                                                <input
                                                    v-model="entry.username"
                                                    type="text"
                                                    placeholder="admin@example.com"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                />
                                            </div>
                                            
                                            <!-- Password -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                                    Password
                                                </label>
                                                <div class="flex rounded-md shadow-sm">
                                                    <input
                                                        v-model="entry.password"
                                                        :type="entry.showPassword ? 'text' : 'password'"
                                                        placeholder="Enter password"
                                                        class="flex-1 rounded-l-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                    />
                                                    <button
                                                        type="button"
                                                        @click="entry.showPassword = !entry.showPassword"
                                                        class="inline-flex items-center px-2 py-1 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-xs rounded-r-md hover:bg-gray-100"
                                                    >
                                                        <svg v-if="entry.showPassword" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                                        </svg>
                                                        <svg v-else class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <button
                                                    type="button"
                                                    @click="generatePasswordForEntry(index)"
                                                    class="mt-1 text-xs text-indigo-600 hover:text-indigo-500"
                                                >
                                                    Generate Strong Password
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- URLs/IPs -->
                                        <div class="mt-3">
                                            <div class="flex items-center justify-between mb-2">
                                                <label class="block text-xs font-medium text-gray-600">
                                                    URLs/IP Addresses
                                                </label>
                                                <button
                                                    type="button"
                                                    @click="addUrlToEntry(index)"
                                                    class="text-xs text-blue-600 hover:text-blue-800"
                                                >
                                                    + Add URL/IP
                                                </button>
                                            </div>
                                            
                                            <div class="space-y-2">
                                                <div
                                                    v-for="(url, urlIndex) in entry.urls"
                                                    :key="urlIndex"
                                                    class="flex items-center space-x-2"
                                                >
                                                    <input
                                                        v-model="url.value"
                                                        type="text"
                                                        placeholder="https://example.com or 192.168.1.1"
                                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                    />
                                                    <button
                                                        v-if="entry.urls.length > 1"
                                                        type="button"
                                                        @click="removeUrlFromEntry(index, urlIndex)"
                                                        class="text-red-500 hover:text-red-700"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
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
    credentialEntries.push({
        id: entryIdCounter++,
        username: '',
        password: '',
        showPassword: false,
        urls: [{ value: '' }]
    });
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