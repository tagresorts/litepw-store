<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ widget.title }}</h3>
                <div class="flex items-center space-x-2">
                    <button
                        @click="refreshWidget"
                        class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
                        title="Refresh"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                    <button
                        @click="showSettings = !showSettings"
                        class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
                        title="Settings"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="p-4">
            <!-- Activity Log Widget -->
            <div v-if="widget.type === 'activity_log'" class="space-y-3">
                <div
                    v-for="log in widget.data.logs"
                    :key="log.id"
                    class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                    <div class="flex-shrink-0">
                        <div :class="getActionIconClass(log.action)" class="w-8 h-8 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="log.action === 'create'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                <path v-else-if="log.action === 'update'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                <path v-else-if="log.action === 'delete'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ log.user }} {{ getActionText(log.action) }} {{ log.resource_type }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ log.resource_name }}</p>
                    </div>
                    <div class="text-xs text-gray-400 dark:text-gray-500">
                        {{ log.created_at }}
                    </div>
                </div>
                <div v-if="!widget.data.logs || widget.data.logs.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                    No recent activity
                </div>
            </div>

            <!-- Quick Access Widget -->
            <div v-else-if="widget.type === 'quick_access'" class="space-y-4">
                <div v-if="widget.data.favorites && widget.data.favorites.length > 0">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Favorites</h4>
                    <div class="space-y-2">
                        <div
                            v-for="item in widget.data.favorites"
                            :key="item.id"
                            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                        >
                            <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ item.title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ item.group }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="widget.data.recent && widget.data.recent.length > 0">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Recently Accessed</h4>
                    <div class="space-y-2">
                        <div
                            v-for="item in widget.data.recent"
                            :key="item.id"
                            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                        >
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ item.title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ item.group }} • {{ item.last_accessed }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Health Widget -->
            <div v-else-if="widget.type === 'password_health'" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ widget.data.expired || 0 }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Expired</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ widget.data.expiring || 0 }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Expiring Soon</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ widget.data.weak || 0 }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Weak</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ (widget.data.total || 0) - (widget.data.expired || 0) - (widget.data.expiring || 0) - (widget.data.weak || 0) }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Healthy</div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-gray-500 dark:text-gray-400">Overall Health</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ getHealthPercentage(widget.data) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div
                            :class="getHealthBarClass(getHealthPercentage(widget.data))"
                            :style="{ width: getHealthPercentage(widget.data) + '%' }"
                            class="h-2 rounded-full transition-all duration-300"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- Recent Updates Widget -->
            <div v-else-if="widget.type === 'recent_updates'" class="space-y-3">
                <div
                    v-for="update in widget.data.updates"
                    :key="update.id"
                    class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ update.title }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ update.group }} • Updated by {{ update.updated_by }} {{ update.updated_at }}
                        </p>
                    </div>
                </div>
                <div v-if="!widget.data.updates || widget.data.updates.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                    No recent updates
                </div>
            </div>
        </div>

        <!-- Widget Settings Modal -->
        <div v-if="showSettings" class="absolute inset-0 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-medium text-gray-900 dark:text-white">Widget Settings</h4>
                <button
                    @click="showSettings = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                    <input
                        v-model="localTitle"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button
                        @click="showSettings = false"
                        class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                    <button
                        @click="saveSettings"
                        class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface Widget {
    id: number;
    type: string;
    title: string;
    config: any;
    position: {
        x: number;
        y: number;
        w: number;
        h: number;
    };
    data: any;
}

interface Props {
    widget: Widget;
}

const props = defineProps<Props>();

const showSettings = ref(false);
const localTitle = ref(props.widget.title);

const refreshWidget = () => {
    // Implement widget refresh logic
    console.log('Refreshing widget:', props.widget.id);
};

const saveSettings = () => {
    // Implement save settings logic
    console.log('Saving widget settings');
    showSettings.value = false;
};

const getActionIconClass = (action: string) => {
    const classes = {
        create: 'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400',
        update: 'bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400',
        delete: 'bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400',
        view: 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
    };
    return classes[action as keyof typeof classes] || 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
};

const getActionText = (action: string) => {
    const texts = {
        create: 'created',
        update: 'updated',
        delete: 'deleted',
        view: 'viewed',
    };
    return texts[action as keyof typeof texts] || action;
};

const getHealthPercentage = (data: any) => {
    const total = data.total || 0;
    if (total === 0) return 100;
    
    const unhealthy = (data.expired || 0) + (data.expiring || 0) + (data.weak || 0);
    const healthy = total - unhealthy;
    return Math.round((healthy / total) * 100);
};

const getHealthBarClass = (percentage: number) => {
    if (percentage >= 80) return 'bg-green-500';
    if (percentage >= 60) return 'bg-yellow-500';
    if (percentage >= 40) return 'bg-orange-500';
    return 'bg-red-500';
};
</script>