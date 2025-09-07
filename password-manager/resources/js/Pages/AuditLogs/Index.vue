<template>
    <Head title="Audit Logs" />

    <SidebarLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Audit Logs
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium">Security Activity</h3>
                            <div class="flex items-center space-x-4">
                                <select class="rounded-md border-gray-300 text-sm">
                                    <option>All Activities</option>
                                    <option>Login</option>
                                    <option>Credential Access</option>
                                    <option>Credential Creation</option>
                                    <option>Credential Update</option>
                                    <option>Credential Deletion</option>
                                </select>
                                <input
                                    type="date"
                                    class="rounded-md border-gray-300 text-sm"
                                    placeholder="Filter by date"
                                >
                            </div>
                        </div>

                        <div v-if="auditLogs.data.length === 0" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-lg font-medium mb-2">No audit logs found</p>
                            <p>Activity will appear here as you use the password manager.</p>
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="log in auditLogs.data"
                                :key="log.id"
                                class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-8 h-8 rounded-full flex items-center justify-center"
                                                :class="getActivityIcon(log.activity).class"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        :d="getActivityIcon(log.activity).path"
                                                    />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ log.description }}
                                            </p>
                                            <div class="mt-1 text-xs text-gray-500 space-x-4">
                                                <span>{{ log.user_name }}</span>
                                                <span>{{ formatDate(log.created_at) }}</span>
                                                <span v-if="log.ip_address">{{ log.ip_address }}</span>
                                            </div>
                                            <div v-if="log.metadata" class="mt-2">
                                                <details class="text-xs">
                                                    <summary class="cursor-pointer text-gray-500 hover:text-gray-700">
                                                        View Details
                                                    </summary>
                                                    <pre class="mt-1 p-2 bg-gray-100 rounded text-gray-600 overflow-x-auto">{{ JSON.stringify(log.metadata, null, 2) }}</pre>
                                                </details>
                                            </div>
                                        </div>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                        :class="getActivityBadge(log.activity)"
                                    >
                                        {{ log.activity }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="auditLogs.links && auditLogs.links.length > 3" class="mt-6">
                            <nav class="flex justify-center">
                                <div class="flex space-x-1">
                                    <Link
                                        v-for="link in auditLogs.links"
                                        :key="link.label"
                                        :href="link.url || '#'"
                                        :class="[
                                            'px-3 py-2 text-sm font-medium rounded-md',
                                            link.active
                                                ? 'bg-blue-600 text-white'
                                                : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'
                                        ]"
                                        v-html="link.label"
                                    />
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';

interface AuditLog {
    id: number;
    activity: string;
    description: string;
    user_name: string;
    ip_address: string;
    metadata: any;
    created_at: string;
}

interface PaginatedAuditLogs {
    data: AuditLog[];
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
    meta: {
        current_page: number;
        total: number;
        per_page: number;
    };
}

interface Props {
    auditLogs: PaginatedAuditLogs;
}

defineProps<Props>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString();
};

const getActivityIcon = (activity: string) => {
    const icons: Record<string, { path: string; class: string }> = {
        'login': {
            path: 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',
            class: 'bg-green-100 text-green-600'
        },
        'logout': {
            path: 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
            class: 'bg-red-100 text-red-600'
        },
        'credential_created': {
            path: 'M12 4v16m8-8H4',
            class: 'bg-blue-100 text-blue-600'
        },
        'credential_updated': {
            path: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
            class: 'bg-yellow-100 text-yellow-600'
        },
        'credential_deleted': {
            path: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
            class: 'bg-red-100 text-red-600'
        },
        'credential_accessed': {
            path: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
            class: 'bg-purple-100 text-purple-600'
        }
    };
    
    return icons[activity] || {
        path: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        class: 'bg-gray-100 text-gray-600'
    };
};

const getActivityBadge = (activity: string) => {
    const badges: Record<string, string> = {
        'login': 'bg-green-100 text-green-800',
        'logout': 'bg-red-100 text-red-800',
        'credential_created': 'bg-blue-100 text-blue-800',
        'credential_updated': 'bg-yellow-100 text-yellow-800',
        'credential_deleted': 'bg-red-100 text-red-800',
        'credential_accessed': 'bg-purple-100 text-purple-800'
    };
    
    return badges[activity] || 'bg-gray-100 text-gray-800';
};
</script>