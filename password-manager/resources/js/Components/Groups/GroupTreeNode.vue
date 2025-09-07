<template>
    <div class="group-tree-node">
        <!-- Current Group -->
        <div 
            class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            :style="{ marginLeft: `${level * 20}px` }"
        >
            <div class="flex items-center space-x-3">
                <!-- Expand/Collapse Button -->
                <button
                    v-if="hasChildren"
                    @click="toggleExpanded"
                    class="p-1 text-gray-400 hover:text-gray-600 rounded"
                >
                    <svg 
                        class="w-4 h-4 transition-transform"
                        :class="{ 'rotate-90': expanded }"
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                
                <!-- Group Icon -->
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                
                <!-- Group Info -->
                <div>
                    <h4 class="font-medium text-gray-900">{{ group.name }}</h4>
                    <p v-if="group.description" class="text-sm text-gray-600">{{ group.description }}</p>
                    <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z" />
                            </svg>
                            {{ group.credential_count }} credentials
                        </span>
                        <span>Level {{ group.level }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex items-center space-x-2">
                <Link
                    :href="route('groups.show', group.id)"
                    class="p-2 text-gray-400 hover:text-gray-600 rounded-md hover:bg-white"
                    title="View"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </Link>
                <Link
                    :href="route('groups.edit', group.id)"
                    class="p-2 text-gray-400 hover:text-gray-600 rounded-md hover:bg-white"
                    title="Edit"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </Link>
                <Link
                    :href="route('groups.export', group.id)"
                    class="p-2 text-blue-400 hover:text-blue-600 rounded-md hover:bg-white"
                    title="Export"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </Link>
                <button
                    @click="$emit('delete', group.id)"
                    class="p-2 text-red-400 hover:text-red-600 rounded-md hover:bg-white"
                    title="Delete"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Children Groups -->
        <div v-if="hasChildren && expanded" class="mt-2">
            <GroupTreeNode
                v-for="child in children"
                :key="child.id"
                :group="child"
                :all-groups="allGroups"
                :level="level + 1"
                @delete="$emit('delete', $event)"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';

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
    group: Group;
    allGroups: Group[];
    level?: number;
}

const props = withDefaults(defineProps<Props>(), {
    level: 0
});

const emit = defineEmits<{
    delete: [groupId: number]
}>();

const expanded = ref(true);

const children = computed(() => {
    return props.allGroups.filter(group => group.parent_id === props.group.id);
});

const hasChildren = computed(() => {
    return children.value.length > 0;
});

const toggleExpanded = () => {
    expanded.value = !expanded.value;
};
</script>

<style scoped>
.group-tree-node {
    position: relative;
}

.group-tree-node::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 1px;
    background-color: #e5e7eb;
    opacity: 0.5;
}

.group-tree-node:last-child::before {
    display: none;
}
</style>