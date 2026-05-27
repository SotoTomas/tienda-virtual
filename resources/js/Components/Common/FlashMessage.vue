<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import CartDrawer from '@/Components/Cart/CartDrawer.vue'
import MobileMenu from '@/Components/Navigation/MobileMenu.vue'
import FlashMessage from '@/Components/Common/FlashMessage.vue'

const page       = usePage()
const user       = computed(() => page.props.auth?.user)
const cartCount  = computed(() => page.props.cart?.count ?? 0)
const categories = computed(() => page.props.categories ?? [])
const cart       = computed(() => page.props.cart ?? { items: [], total: 0, item_count: 0 })

const cartOpen    = ref(false)
const menuOpen    = ref(false)
const searchOpen  = ref(false)
const searchQuery = ref('')

function submitSearch() {
    if (searchQuery.value.trim().length < 2) return
    window.location.href = `/buscar?q=${encodeURIComponent(searchQuery.value)}`
}
</script>

<template>
    <div class="min-h-screen flex flex-col">

        <!-- Flash messages -->
        <FlashMessage />

        <!-- ── Header ───────────────────────────────────────── -->
        <header class="sticky top-0 z-40 bg-stone-50/95 backdrop-blur-sm border-b border-stone-200">

            <!-- Barra superior -->
            <div class="border-b border-stone-100 bg-stone-900 text-stone-300 text-xs text-center py-2 tracking-widest">
                ENVÍO GRATIS EN COMPRAS MAYORES A $50.000
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">

                    <!-- Mobile: hamburger -->
                    <button @click="menuOpen = true" class="lg:hidden btn-ghost p-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <!-- Logo -->
                    <Link :href="route('store.home')" class="font-display text-2xl text-stone-900 tracking-tight">
                        Tienda<span class="text-brand-500">.</span>
                    </Link>

                    <!-- Nav desktop -->
                    <nav class="hidden lg:flex items-center gap-8">
                        <div v-for="cat in categories" :key="cat.id" class="relative group">
                            <Link
                                :href="route('categories.show', cat.slug)"
                                class="text-sm text-stone-600 hover:text-stone-900 transition-colors py-6 tracking-wide"
                            >
                                {{ cat.name }}
                            </Link>
                            <!-- Dropdown subcategorías -->
                            <div v-if="cat.children?.length"
                                class="absolute top-full left-0 w-48 bg-white border border-stone-100 shadow-lg
                                       opacity-0 invisible group-hover:opacity-100 group-hover:visible
                                       transition-all duration-200 translate-y-1 group-hover:translate-y-0">
                                <Link
                                    v-for="child in cat.children"
                                    :key="child.id"
                                    :href="route('categories.show', child.slug)"
                                    class="block px-4 py-2.5 text-sm text-stone-600 hover:text-stone-900 hover:bg-stone-50 transition-colors"
                                >
                                    {{ child.name }}
                                </Link>
                            </div>
                        </div>
                        <Link :href="route('products.index')" class="text-sm text-stone-600 hover:text-stone-900 tracking-wide">
                            Todo
                        </Link>
                    </nav>

                    <!-- Acciones -->
                    <div class="flex items-center gap-1">

                        <!-- Search -->
                        <button @click="searchOpen = !searchOpen" class="btn-ghost p-2.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                            </svg>
                        </button>

                        <!-- User -->
                        <Link v-if="user" :href="route('account.addresses.index')" class="btn-ghost p-2.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                        </Link>
                        <Link v-else :href="route('login')" class="btn-ghost p-2.5 hidden sm:flex">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                        </Link>

                        <!-- Wishlist -->
                        <Link v-if="user" :href="route('wishlist.index')" class="btn-ghost p-2.5 hidden sm:flex">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                            </svg>
                        </Link>

                        <!-- Cart -->
                        <button @click="cartOpen = true" class="btn-ghost p-2.5 relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                            </svg>
                            <span v-if="cartCount > 0"
                                class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-brand-500 text-white text-[10px] font-semibold rounded-full flex items-center justify-center">
                                {{ cartCount > 9 ? '9+' : cartCount }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Search bar expandible -->
                <Transition name="search">
                    <div v-if="searchOpen" class="pb-4">
                        <form @submit.prevent="submitSearch" class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Buscar productos..."
                                class="input-base pr-12"
                                autofocus
                            />
                            <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </Transition>
            </div>
        </header>

        <!-- ── Contenido principal ───────────────────────── -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- ── Footer ───────────────────────────────────────── -->
        <footer class="bg-stone-900 text-stone-400 mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                    <!-- Brand -->
                    <div class="md:col-span-1">
                        <span class="font-display text-2xl text-white">
                            Tienda<span class="text-brand-400">.</span>
                        </span>
                        <p class="mt-4 text-sm leading-relaxed">
                            Moda y estilo para cada ocasión. Calidad que se nota.
                        </p>
                    </div>
                    <!-- Categorías -->
                    <div>
                        <h4 class="text-white text-sm font-medium tracking-widest uppercase mb-4">Categorías</h4>
                        <ul class="space-y-2">
                            <li v-for="cat in categories" :key="cat.id">
                                <Link :href="route('categories.show', cat.slug)" class="text-sm hover:text-white transition-colors">
                                    {{ cat.name }}
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <!-- Ayuda -->
                    <div>
                        <h4 class="text-white text-sm font-medium tracking-widest uppercase mb-4">Ayuda</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-white transition-colors">Preguntas frecuentes</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Envíos y devoluciones</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Guía de talles</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Contacto</a></li>
                        </ul>
                    </div>
                    <!-- Mi cuenta -->
                    <div>
                        <h4 class="text-white text-sm font-medium tracking-widest uppercase mb-4">Mi cuenta</h4>
                        <ul class="space-y-2 text-sm">
                            <li v-if="!user"><Link :href="route('login')" class="hover:text-white transition-colors">Iniciar sesión</Link></li>
                            <li v-if="!user"><Link :href="route('register')" class="hover:text-white transition-colors">Registrarse</Link></li>
                            <li v-if="user"><Link :href="route('orders.index')" class="hover:text-white transition-colors">Mis pedidos</Link></li>
                            <li v-if="user"><Link :href="route('wishlist.index')" class="hover:text-white transition-colors">Lista de deseos</Link></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-stone-800 mt-12 pt-8 text-xs text-center">
                    © {{ new Date().getFullYear() }} Tienda Virtual. Todos los derechos reservados.
                </div>
            </div>
        </footer>

        <!-- ── Drawers y overlays ────────────────────────── -->
        <CartDrawer :open="cartOpen" :cart="cart" @close="cartOpen = false" />        
        <MobileMenu :open="menuOpen" :categories="categories" @close="menuOpen = false" />
    </div>
</template>

<style scoped>
.search-enter-active, .search-leave-active { transition: all 0.2s ease; }
.search-enter-from, .search-leave-to { opacity: 0; transform: translateY(-8px); }
</style>