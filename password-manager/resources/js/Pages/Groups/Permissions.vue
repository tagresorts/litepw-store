<template>
    <Head :title="`Permissions - ${group.name}`" />

    <SidebarLayout>
        <template #header>
            Manage Permissions - {{ group.name }}
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="save" class="space-y-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effect</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Export</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Share</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="entry in rows" :key="entry.user_id">
                                            <td class="px-4 py-2">
                                                <div class="text-sm font-medium text-gray-900">{{ entry.name }}</div>
                                                <div class="text-xs text-gray-500">{{ entry.email }}</div>
                                            </td>
                                            <td class="px-4 py-2">
                                                <select v-model="entry.effect" class="rounded-md border-gray-300 text-sm">
                                                    <option value="allow">Allow</option>
                                                    <option value="deny">Deny</option>
                                                </select>
                                            </td>
                                            <td class="px-4 py-2">
                                                <select v-model="entry.level" class="rounded-md border-gray-300 text-sm">
                                                    <option value="read">Read</option>
                                                    <option value="write">Write</option>
                                                    <option value="admin">Admin</option>
                                                </select>
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <input type="checkbox" v-model="entry.can_export" />
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <input type="checkbox" v-model="entry.can_share" />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="flex items-center justify-end">
                                <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';

interface Group { id: number; name: string; }
interface User { id: number; name: string; email: string; }
interface Permission { user_id: number; effect: string; level: string; can_export: boolean; can_share: boolean; }

const props = defineProps<{ group: Group; users: User[]; permissions: Permission[]; }>();

const rows = props.users.map(u => {
    const p = props.permissions.find(x => x.user_id === u.id);
    return {
        user_id: u.id,
        name: u.name,
        email: u.email,
        effect: p?.effect || 'allow',
        level: p?.level || 'read',
        can_export: p?.can_export || false,
        can_share: p?.can_share || false,
    };
});

const form = useForm({ entries: rows });

const save = () => {
    form.post(route('groups.permissions.save', props.group.id));
};
</script>
