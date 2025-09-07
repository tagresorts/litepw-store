<template>
    <div
        :class="sidebarClasses"
        class="fixed left-0 top-0 h-full bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 ease-in-out z-40"
    >
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <div v-if="!collapsed" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <span class="text-lg font-semibold text-gray-900 dark:text-white">PassManager</span>
            </div>
            <div v-else class="flex justify-center w-full">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z" />
                    </svg>
                </div>
            </div>
            
            <div class="flex items-center space-x-1">
                <button
                    @click="$emit('toggle-collapse')"
                    class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
                    :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="collapsed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button
                    v-if="!collapsed"
                    @click="$emit('toggle-dock')"
                    class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
                    :title="docked ? 'Undock sidebar' : 'Dock sidebar'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="docked" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4a1 1 0 011-1h4M4 16v4a1 1 0 001 1h4m8-16h4a1 1 0 011 1v4m-4 12h4a1 1 0 001-1v-4" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto p-4">
            <!-- Quick Actions -->
            <div class="mb-6">
                <h3 v-if="!collapsed" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Quick Actions
                </h3>
                <div class="space-y-1">
                    <Link
                        :href="route('credentials.create')"
                        class="w-full flex items-center space-x-3 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white"
                        :title="collapsed ? 'Add Credential' : ''"
                    >
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span v-if="!collapsed">Add Credential</span>
                    </Link>
                    <Link
                        :href="route('groups.create')"
                        class="w-full flex items-center space-x-3 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white"
                        :title="collapsed ? 'Create Group' : ''"
                    >
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span v-if="!collapsed">Create Group</span>
                    </Link>
                </div>
            </div>

            <!-- Modules -->
            <div class="mb-6">
                <h3 v-if="!collapsed" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Modules
                </h3>
                <div class="space-y-1">
                    <Link
                        :href="route('credentials.index', { module: 'backend' })"
                        class="w-full flex items-center space-x-3 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white"
                        :title="collapsed ? 'Backend' : ''"
                    >
                        <svg class="w-5 h-5 flex-shrink-0 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                        <span v-if="!collapsed">Backend</span>
                    </Link>
                    <Link
                        :href="route('credentials.index', { module: 'cache' })"
                        class="w-full flex items-center space-x-3 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white"
                        :title="collapsed ? 'Cache' : ''"
                    >
                        <svg class="w-5 h-5 flex-shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span v-if="!collapsed">Cache</span>
                    </Link>
                    <Link
                        :href="route('credentials.index', { module: 'database' })"
                        class="w-full flex items-center space-x-3 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white"
                        :title="collapsed ? 'Database' : ''"
                    >
                        <svg class="w-5 h-5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c4.418 0 8 1.79 8 4v10c0 2.21-3.582 4-8 4s-8-1.79-8-4V7c0-2.21 3.582-4 8-4zM4 12c0 2.21 3.582 4 8 4s8-1.79 8-4" />
                        </svg>
                        <span v-if="!collapsed">Database</span>
                    </Link>
                </div>
            </div>

            <!-- Navigation Tree -->
            <div>
                <h3 v-if="!collapsed" class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Groups
                </h3>
                <div class="space-y-1">
                    <NavigationItem
                        v-for="item in navigationTree"
                        :key="item.id"
                        :item="item"
                        :collapsed="collapsed"
                        :level="0"
                    />
                </div>
            </div>

            <!-- Favorites -->
            <div v-if="!collapsed" class="mt-6">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Favorites
                </h3>
                <div class="space-y-1">
                    <button class="w-full flex items-center space-x-3 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                        <span class="truncate">Database Server</span>
                    </button>
                    <button class="w-full flex items-center space-x-3 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                        <span class="truncate">Redis Cache</span>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Footer -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div v-if="!collapsed" class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">Admin User</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">admin@example.com</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import NavigationItem from './NavigationItem.vue';

interface NavigationTreeItem {
    id: number;
    name: string;
    description: string;
    level: number;
    credential_count: number;
    children: NavigationTreeItem[];
}

interface Props {
    collapsed: boolean;
    docked: boolean;
    navigationTree: NavigationTreeItem[];
}

const props = defineProps<Props>();

defineEmits<{
    'toggle-collapse': [];
    'toggle-dock': [];
}>();

const sidebarClasses = computed(() => {
    return {
        'w-64': !props.collapsed,
        'w-16': props.collapsed,
    };
});
</script>