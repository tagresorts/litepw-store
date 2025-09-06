<template>
    <div>
        <button
            @click="toggleExpanded"
            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white"
            :class="{ 'pl-6': level > 0 && !collapsed }"
            :title="collapsed ? item.name : ''"
        >
            <div class="flex items-center space-x-3 flex-1 min-w-0">
                <!-- Folder Icon -->
                <svg
                    class="w-4 h-4 flex-shrink-0"
                    :class="{ 'text-blue-500': hasChildren, 'text-gray-400': !hasChildren }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path v-if="hasChildren" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                
                <div v-if="!collapsed" class="flex-1 min-w-0">
                    <span class="truncate">{{ item.name }}</span>
                    <span v-if="item.credential_count > 0" class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        {{ item.credential_count }}
                    </span>
                </div>
            </div>

            <!-- Expand/Collapse Arrow -->
            <svg
                v-if="hasChildren && !collapsed"
                class="w-4 h-4 flex-shrink-0 transition-transform duration-200"
                :class="{ 'rotate-90': isExpanded }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <!-- Children -->
        <div
            v-if="hasChildren && isExpanded && !collapsed"
            class="ml-3 border-l border-gray-200 dark:border-gray-700 pl-3 mt-1 space-y-1"
        >
            <NavigationItem
                v-for="child in item.children"
                :key="child.id"
                :item="child"
                :collapsed="collapsed"
                :level="level + 1"
            />
        </div>

        <!-- Tooltip for collapsed state -->
        <div
            v-if="collapsed && showTooltip"
            class="absolute left-16 bg-gray-900 dark:bg-gray-700 text-white text-sm px-2 py-1 rounded shadow-lg z-50 whitespace-nowrap"
            :style="{ top: tooltipPosition + 'px' }"
        >
            {{ item.name }}
            <span v-if="item.credential_count > 0" class="text-gray-300"> ({{ item.credential_count }})</span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';

interface NavigationTreeItem {
    id: number;
    name: string;
    description: string;
    level: number;
    credential_count: number;
    children: NavigationTreeItem[];
}

interface Props {
    item: NavigationTreeItem;
    collapsed: boolean;
    level: number;
}

const props = defineProps<Props>();

const isExpanded = ref(false);
const showTooltip = ref(false);
const tooltipPosition = ref(0);

const hasChildren = computed(() => {
    return props.item.children && props.item.children.length > 0;
});

const toggleExpanded = () => {
    if (hasChildren.value && !props.collapsed) {
        isExpanded.value = !isExpanded.value;
    }
};

const handleMouseEnter = (event: MouseEvent) => {
    if (props.collapsed) {
        showTooltip.value = true;
        tooltipPosition.value = (event.target as HTMLElement).offsetTop;
    }
};

const handleMouseLeave = () => {
    showTooltip.value = false;
};
</script>