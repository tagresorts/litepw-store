<template>
    <Head title="Groups" />

    <SidebarLayout>
        <template #header>
            Groups
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium">Manage Groups</h3>
                            <Link
                                :href="route('groups.create')"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Group
                            </Link>
                        </div>

                        <div v-if="groups.length === 0" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <p class="text-lg font-medium mb-2">No groups found</p>
                            <p class="mb-4">Create groups to organize your credentials.</p>
                            <Link
                                :href="route('groups.create')"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                            >
                                Create Your First Group
                            </Link>
                        </div>

                        <div v-else>
                            <!-- Tree View -->
                            <div class="space-y-2">
                                <div
                                    v-for="group in rootGroups"
                                    :key="group.id"
                                    class="group-item"
                                >
                                    <GroupTreeNode
                                        :group="group"
                                        :all-groups="groups"
                                        @delete="deleteGroup"
                                    />
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
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import GroupTreeNode from '@/Components/Groups/GroupTreeNode.vue';

interface Group {
    id: number;
    name: string;
    description: string;
    parent_id: number | null;
    level: number;
    credential_count: number;
    created_at: string;
    updated_at: string;
}

interface Props {
    groups: Group[];
    navigationTree: any[];
}

const props = defineProps<Props>();

const rootGroups = computed(() => {
    return props.groups.filter(group => group.parent_id === null);
});

const deleteGroup = (groupId: number) => {
    if (confirm('Are you sure you want to delete this group? This will not delete the credentials in it.')) {
        router.delete(route('groups.destroy', groupId));
    }
};
</script>

<style scoped>
.group-item {
    position: relative;
}

.group-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 0;
    top: 50px;
    bottom: -10px;
    width: 1px;
    background-color: #e5e7eb;
    opacity: 0.3;
}
</style>