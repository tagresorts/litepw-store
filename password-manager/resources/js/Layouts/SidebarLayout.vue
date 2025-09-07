<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import NavigationSidebar from '@/Components/Navigation/NavigationSidebar.vue';

interface NavigationItem {
    id: number;
    name: string;
    description: string;
    level: number;
    credential_count: number;
    children: NavigationItem[];
}

interface Props {
    navigationTree?: NavigationItem[];
}

const props = defineProps<Props>();

const sidebarCollapsed = ref(false);
const sidebarDocked = ref(true);
const isDarkMode = ref(false);
const searchQuery = ref('');
const showUserMenu = ref(false);

// Toggle sidebar
const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
};

const toggleSidebarDock = () => {
    sidebarDocked.value = !sidebarDocked.value;
};

// Toggle dark mode
const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    document.documentElement.classList.toggle('dark', isDarkMode.value);
    try {
        localStorage.setItem('pm_theme', isDarkMode.value ? 'dark' : 'light');
    } catch (e) {
        // ignore storage failures
    }
};

// Search functionality
const performSearch = () => {
    if (searchQuery.value.trim()) {
        router.visit(route('search', { q: searchQuery.value.trim() }));
    }
};

const clearSearch = () => {
    searchQuery.value = '';
};

// User menu functionality
const toggleUserMenu = () => {
    showUserMenu.value = !showUserMenu.value;
};

// Computed classes for main content area
const mainContentClasses = computed(() => {
    return {
        'ml-64': sidebarDocked.value && !sidebarCollapsed.value,
        'ml-16': sidebarDocked.value && sidebarCollapsed.value,
        'ml-0': !sidebarDocked.value,
        'transition-all duration-300 ease-in-out': true,
    };
});

onMounted(() => {
    // Load persisted theme from localStorage first
    try {
        const storedTheme = localStorage.getItem('pm_theme');
        if (storedTheme === 'dark') {
            isDarkMode.value = true;
        } else if (storedTheme === 'light') {
            isDarkMode.value = false;
        }
        (window as any).__pmStoredTheme = storedTheme;
    } catch (e) {
        // ignore storage failures
    }

    // Load user preferences for sidebar and theme (fallbacks)
    const preferences = usePage().props.auth?.user?.preferences;
    if (preferences) {
        sidebarCollapsed.value = preferences.sidebar_collapsed || false;
        sidebarDocked.value = preferences.sidebar_docked !== false;
        if ((window as any).__pmStoredTheme == null) {
            isDarkMode.value = preferences.dark_mode || false;
        }
    }

    // Apply theme class
    document.documentElement.classList.toggle('dark', isDarkMode.value);

    // Close user menu when clicking outside
    const handleClickOutside = (event: Event) => {
        const userMenu = document.querySelector('.user-menu-container');
        if (userMenu && !userMenu.contains(event.target as Node)) {
            showUserMenu.value = false;
        }
    };

    document.addEventListener('click', handleClickOutside);
    
    // Cleanup event listener on unmount
    return () => {
        document.removeEventListener('click', handleClickOutside);
    };
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Navigation Sidebar -->
        <NavigationSidebar
            :collapsed="sidebarCollapsed"
            :docked="sidebarDocked"
            :navigation-tree="navigationTree || []"
            @toggle-collapse="toggleSidebar"
            @toggle-dock="toggleSidebarDock"
        />

        <!-- Main Content -->
        <div :class="mainContentClasses">
            <!-- Top Header -->
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button
                                v-if="!sidebarDocked"
                                @click="toggleSidebar"
                                class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                                <slot name="header" />
                            </h1>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <!-- Search Bar -->
                            <div class="relative">
                                <input
                                    v-model="searchQuery"
                                    @keyup.enter="performSearch"
                                    type="text"
                                    placeholder="Search credentials..."
                                    class="w-64 px-4 py-2 pl-10 pr-4 text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                                >
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <button
                                    v-if="searchQuery"
                                    @click="clearSearch"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3"
                                >
                                    <svg class="w-4 h-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Dark Mode Toggle -->
                            <button
                                @click="toggleDarkMode"
                                class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700"
                            >
                                <svg v-if="!isDarkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                                <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </button>

                            <!-- User Menu -->
                            <div class="relative user-menu-container">
                                <button
                                    @click="toggleUserMenu"
                                    class="flex items-center space-x-2 p-2 rounded-md text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                                >
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <span class="hidden md:block">{{ $page.props.auth.user.name }}</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div
                                    v-if="showUserMenu"
                                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50 border border-gray-200 dark:border-gray-700"
                                >
                                    <Link
                                        :href="route('profile.edit')"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                    >
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span>Profile</span>
                                        </div>
                                    </Link>
                                    <Link
                                        :href="route('credentials.index')"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                    >
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z" />
                                            </svg>
                                            <span>All Credentials</span>
                                        </div>
                                    </Link>
                                    <Link
                                        :href="route('groups.index')"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                    >
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                            <span>Groups</span>
                                        </div>
                                    </Link>
                                    <Link
                                        :href="route('audit-logs.index')"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                    >
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span>Audit Logs</span>
                                        </div>
                                    </Link>
                                    <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                                    <Link
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                                    >
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            <span>Log Out</span>
                                        </div>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>