<template>
    <Head title="Edit Group" />

    <SidebarLayout>
        <template #header>
            Edit Group
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Group Details Form -->
                <div class="mb-8 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Group Name *
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': form.errors.name }"
                                    required
                                />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': form.errors.description }"
                                ></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <div>
                                <label for="parent_id" class="block text-sm font-medium text-gray-700">
                                    Parent Group
                                </label>
                                <select
                                    id="parent_id"
                                    v-model="form.parent_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">No parent (top-level group)</option>
                                    <option
                                        v-for="availableGroup in availableGroups"
                                        :key="availableGroup.id"
                                        :value="availableGroup.id"
                                    >
                                        {{ '  '.repeat(availableGroup.level) }}{{ availableGroup.name }}
                                    </option>
                                </select>
                                <p class="mt-1 text-sm text-gray-500">
                                    Select a parent group to create a nested structure
                                </p>
                                <p v-if="form.errors.parent_id" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.parent_id }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between pt-6 border-t">
                                <Link
                                    :href="route('groups.index')"
                                    class="text-gray-600 hover:text-gray-800"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50"
                                >
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ form.processing ? 'Updating...' : 'Update Group' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Credentials Management Section -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-6">Manage Credentials in this Group</h3>
                        
                        <div v-if="credentials.length === 0" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z" />
                            </svg>
                            <p class="text-lg font-medium mb-2">No credentials in this group</p>
                            <p class="mb-4">Add credentials to this group to manage them here.</p>
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

                        <div v-else>
                            <!-- Bulk Actions -->
                            <div class="mb-6 flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            v-model="selectAll"
                                            @change="toggleSelectAll"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Select All</span>
                                    </label>
                                    <span class="text-sm text-gray-500">{{ selectedCredentials.length }} selected</span>
                                </div>
                                
                                <div v-if="selectedCredentials.length > 0" class="flex items-center space-x-2">
                                    <select
                                        v-model="moveToGroupId"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    >
                                        <option value="">Move to group...</option>
                                        <option
                                            v-for="group in availableGroups"
                                            :key="group.id"
                                            :value="group.id"
                                            :disabled="group.id === props.group.id"
                                        >
                                            {{ group.name }}
                                        </option>
                                        <option value="null">Ungrouped</option>
                                    </select>
                                    <button
                                        @click="moveSelectedCredentials"
                                        :disabled="!moveToGroupId || moveCredentialsForm.processing"
                                        class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 disabled:opacity-50"
                                    >
                                        <svg v-if="moveCredentialsForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Move
                                    </button>
                                </div>
                            </div>

                            <!-- Credentials List -->
                            <div class="space-y-3">
                                <div
                                    v-for="credential in credentials"
                                    :key="credential.id"
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    <div class="flex items-center space-x-3">
                                        <input
                                            type="checkbox"
                                            :value="credential.id"
                                            v-model="selectedCredentials"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        />
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ credential.title }}</h4>
                                            <p class="text-sm text-gray-600">{{ credential.username }}</p>
                                            <p v-if="credential.url" class="text-xs text-gray-500">{{ credential.url }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
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
                                            @click="moveSingleCredential(credential.id)"
                                            class="p-2 text-blue-400 hover:text-blue-600 rounded-md hover:bg-white"
                                            title="Move to another group"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
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
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';

interface Group {
    id: number;
    name: string;
    description: string;
    parent_id: number | null;
    level: number;
}

interface Credential {
    id: number;
    title: string;
    username: string;
    url: string;
    notes: string;
}

interface Props {
    group: Group;
    availableGroups: Group[];
    credentials: Credential[];
    navigationTree: any[];
}

const props = defineProps<Props>();

const form = useForm({
    name: props.group.name,
    description: props.group.description || '',
    parent_id: props.group.parent_id || '',
});

const moveCredentialsForm = useForm({
    credential_ids: [] as number[],
    target_group_id: null as number | null,
});

const selectedCredentials = ref<number[]>([]);
const selectAll = ref(false);
const moveToGroupId = ref<string>('');

const submit = () => {
    form.patch(route('groups.update', props.group.id));
};

const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedCredentials.value = props.credentials.map(c => c.id);
    } else {
        selectedCredentials.value = [];
    }
};

const moveSelectedCredentials = () => {
    if (!moveToGroupId.value || selectedCredentials.value.length === 0) return;
    
    const targetGroupId = moveToGroupId.value === 'null' ? null : parseInt(moveToGroupId.value);
    
    moveCredentialsForm.credential_ids = selectedCredentials.value;
    moveCredentialsForm.target_group_id = targetGroupId;
    
    moveCredentialsForm.post(route('groups.move', props.group.id), {
        onSuccess: () => {
            selectedCredentials.value = [];
            selectAll.value = false;
            moveToGroupId.value = '';
            // Refresh the page to show updated credentials
            router.reload();
        }
    });
};

const moveSingleCredential = (credentialId: number) => {
    // For single credential move, we'll redirect to credential edit page
    // where user can change the group
    router.visit(route('credentials.edit', credentialId));
};
</script>