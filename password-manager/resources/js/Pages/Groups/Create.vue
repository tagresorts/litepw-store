<template>
    <Head title="Create Group" />

    <SidebarLayout>
        <template #header>
            Create New Group
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
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
                                    placeholder="e.g., Work, Personal, Banking"
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
                                    placeholder="Optional description for this group"
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
                                    <option v-for="group in props.groups" :key="group.id" :value="group.id">
                                        {{ '  '.repeat(group.level || 0) }}{{ group.name }}
                                    </option>
                                </select>
                                <p class="mt-1 text-sm text-gray-500">
                                    Select a parent group to create a nested structure
                                    <span v-if="props.groups.length === 0" class="text-orange-600"> (No groups available yet)</span>
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
                                    {{ form.processing ? 'Creating...' : 'Create Group' }}
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
import { Head, Link, useForm } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';

interface Group {
    id: number;
    name: string;
    level: number;
}

interface Props {
    groups: Group[];
    navigationTree: any[];
}

const props = defineProps<Props>();

const form = useForm({
    name: '',
    description: '',
    parent_id: '',
});

const submit = () => {
    form.post(route('groups.store'));
};
</script>