<template>
    <div class="space-y-4">
        <div
            v-for="(entry, index) in entries"
            :key="entry.id"
            class="border border-gray-200 rounded-lg p-4 bg-gray-50"
        >
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-medium text-gray-700">
                    Entry {{ index + 1 }}
                </h4>
                <button
                    v-if="entries.length > 1"
                    @click="$emit('remove-entry', index)"
                    type="button"
                    class="text-red-600 hover:text-red-800"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Username -->
                <div>
                    <label :for="`username-${entry.id}`" class="block text-sm font-medium text-gray-700">
                        Username *
                    </label>
                    <input
                        :id="`username-${entry.id}`"
                        v-model="entry.username"
                        type="text"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                </div>

                <!-- Password -->
                <div>
                    <label :for="`password-${entry.id}`" class="block text-sm font-medium text-gray-700">
                        Password *
                    </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <input
                            :id="`password-${entry.id}`"
                            v-model="entry.password"
                            :type="entry.showPassword ? 'text' : 'password'"
                            class="flex-1 rounded-l-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        />
                        <button
                            type="button"
                            @click="entry.showPassword = !entry.showPassword"
                            class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 hover:bg-gray-100"
                        >
                            <svg v-if="entry.showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                            </svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            @click="$emit('generate-password', index)"
                            class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-blue-50 text-blue-600 hover:bg-blue-100"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- URLs -->
            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">
                        URLs/IPs
                    </label>
                    <button
                        type="button"
                        @click="$emit('add-url', index)"
                        class="inline-flex items-center px-2 py-1 text-xs bg-gray-600 text-white rounded hover:bg-gray-700"
                    >
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add URL
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
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <button
                            v-if="entry.urls.length > 1"
                            @click="$emit('remove-url', index, urlIndex)"
                            type="button"
                            class="text-red-600 hover:text-red-800"
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
</template>

<script setup lang="ts">
interface CredentialEntry {
    id: number;
    username: string;
    password: string;
    showPassword: boolean;
    urls: Array<{ value: string }>;
}

interface Props {
    entries: CredentialEntry[];
}

defineProps<Props>();

defineEmits<{
    'remove-entry': [index: number];
    'generate-password': [index: number];
    'add-url': [index: number];
    'remove-url': [index: number, urlIndex: number];
}>();
</script>