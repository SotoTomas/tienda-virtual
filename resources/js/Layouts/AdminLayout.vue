<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const sidebarOpen = ref(false)

const navigation = [
    {
        label: 'Dashboard',
        route: 'admin.dashboard',
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    },
    {
        label: 'Productos',
        route: 'admin.products.index',
        icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    },
    {
        label: 'Categorías',
        route: 'admin.categories.index',
        icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
    },
    {
        label: 'Pedidos',
        route: 'admin.orders.index',
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
    },
    {
        label: 'Cupones',
        route: 'admin.coupons.index',
        icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
    },
    {
        label: 'Reseñas',
        route: 'admin.reviews.index',
        icon: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
    },
]

function isActive(routeName) {
    return route().current(routeName) || route().current(routeName + '.*')
}

function logout() {
    router.post(route('logout'))
}
</script>

<template>
    <div class="min-h-screen bg-stone-100 flex">

        <!-- ── Sidebar ─────────────────────────────────────── -->
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 bg-stone-900 flex flex-col transform transition-transform duration-300 lg:translate-x-0 lg:static lg:z-auto"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Logo -->
            <div class="px-6 py-6 border-b border-stone-800">
                <Link :href="route('admin.dashboard')" class="font-display text-xl text-white">
                    Tienda<span class="text-brand-400">.</span>
                    <span class="text-stone-400 text-sm font-sans font-normal ml-2">Admin</span>
                </Link>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <Link
                    v-for="item in navigation"
                    :key="item.route"
                    :href="route(item.route)"
                    class="flex items-center gap-3 px-4 py-2.5 rounded text-sm transition-colors"
                    :class="isActive(item.route)
                        ? 'bg-stone-800 text-white'
                        : 'text-stone-400 hover:text-white hover:bg-stone-800'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="item.icon"/>
                    </svg>
                    {{ item.label }}
                </Link>
            </nav>

            <!-- User -->
            <div class="px-4 py-4 border-t border-stone-800">
                <div class="flex items-center gap-3 px-4 py-2">
                    <div class="w-8 h-8 rounded-full bg-brand-500 flex items-center justify-center text-white text-xs font-semibold shrink-0">
                        {{ user?.name?.charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-white truncate">{{ user?.name }}</p>
                        <p class="text-xs text-stone-400 truncate">{{ user?.email }}</p>
                    </div>
                </div>
                <div class="flex gap-2 mt-2 px-4">
                    <Link :href="route('store.home')" target="_blank"
                        class="text-xs text-stone-400 hover:text-white transition-colors flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Ver tienda
                    </Link>
                    <span class="text-stone-700">·</span>
                    <button @click="logout" class="text-xs text-stone-400 hover:text-red-400 transition-colors">
                        Salir
                    </button>
                </div>
            </div>
        </aside>

        <!-- Overlay mobile -->
        <div v-if="sidebarOpen" @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-stone-900/50 lg:hidden"/>

        <!-- ── Contenido ───────────────────────────────────── -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Topbar -->
            <header class="bg-white border-b border-stone-200 px-6 py-4 flex items-center gap-4 sticky top-0 z-30">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-stone-500 hover:text-stone-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Breadcrumb -->
                <div class="flex-1 text-sm text-stone-500">
                    <slot name="breadcrumb" />
                </div>
            </header>

            <!-- Main -->
            <main class="flex-1 p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
