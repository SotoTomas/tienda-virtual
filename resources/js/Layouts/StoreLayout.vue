<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import CartDrawer from '@/Components/Cart/CartDrawer.vue'
import MobileMenu from '@/Components/Navigation/MobileMenu.vue'
import FlashMessage from '@/Components/Common/FlashMessage.vue'
import { useCatalogStore } from '@/stores/catalog'
import { useCartStore } from '@/stores/cart'

const router = useRouter()
const catalog = useCatalogStore()
const cartStore = useCartStore()

const categories = ref([])
const cartCount = computed(() => cartStore.count)

const cartOpen = ref(false)
const menuOpen = ref(false)
const searchOpen = ref(false)
const searchQuery = ref('')

onMounted(async () => {
    await catalog.ensureLoaded()
    categories.value = catalog.getNavigationCategories()
})

function submitSearch() {
    if (searchQuery.value.trim().length < 2) return
    searchOpen.value = false
    router.push({ name: 'search', query: { q: searchQuery.value.trim() } })
}
</script>

<template>
    <div class="min-h-screen flex flex-col">

        <FlashMessage />

        <header class="sticky top-0 z-40 bg-stone-50/95 backdrop-blur-sm border-b border-stone-200">
            <div class="border-b border-stone-100 bg-stone-900 text-stone-300 text-xs text-center py-2 tracking-widest">
                ENVÍO GRATIS EN COMPRAS MAYORES A $50.000
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">

                    <button @click="menuOpen = true" class="lg:hidden btn-ghost p-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <RouterLink to="/" class="font-display text-2xl text-stone-900 tracking-tight">
                        Tienda<span class="text-brand-500">.</span>
                    </RouterLink>

                    <nav class="hidden lg:flex items-center gap-8">
                        <div v-for="cat in categories" :key="cat.id" class="relative group">
                            <RouterLink
                                :to="`/categoria/${cat.slug}`"
                                class="text-sm text-stone-600 hover:text-stone-900 transition-colors py-6 tracking-wide"
                            >
                                {{ cat.name }}
                            </RouterLink>
                            <div v-if="cat.children?.length"
                                class="absolute top-full left-0 w-48 bg-white border border-stone-100 shadow-lg
                                       opacity-0 invisible group-hover:opacity-100 group-hover:visible
                                       transition-all duration-200 translate-y-1 group-hover:translate-y-0">
                                <RouterLink
                                    v-for="child in cat.children"
                                    :key="child.id"
                                    :to="`/categoria/${child.slug}`"
                                    class="block px-4 py-2.5 text-sm text-stone-600 hover:text-stone-900 hover:bg-stone-50 transition-colors"
                                >
                                    {{ child.name }}
                                </RouterLink>
                            </div>
                        </div>
                        <RouterLink to="/productos" class="text-sm text-stone-600 hover:text-stone-900 tracking-wide">
                            Todo
                        </RouterLink>
                    </nav>

                    <div class="flex items-center gap-1">

                        <button @click="searchOpen = !searchOpen" class="btn-ghost p-2.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                            </svg>
                        </button>

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

        <main class="flex-1">
            <router-view />
        </main>

        <footer class="bg-stone-900 text-stone-400 mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                    <div class="md:col-span-1">
                        <span class="font-display text-2xl text-white">
                            Tienda<span class="text-brand-400">.</span>
                        </span>
                        <p class="mt-4 text-sm leading-relaxed">
                            Moda y estilo para cada ocasión. Calidad que se nota.
                        </p>
                    </div>
                    <div>
                        <h4 class="text-white text-sm font-medium tracking-widest uppercase mb-4">Categorías</h4>
                        <ul class="space-y-2">
                            <li v-for="cat in categories" :key="cat.id">
                                <RouterLink :to="`/categoria/${cat.slug}`" class="text-sm hover:text-white transition-colors">
                                    {{ cat.name }}
                                </RouterLink>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-white text-sm font-medium tracking-widest uppercase mb-4">Ayuda</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-white transition-colors">Preguntas frecuentes</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Envíos y devoluciones</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Guía de talles</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Contacto</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-white text-sm font-medium tracking-widest uppercase mb-4">Catálogo</h4>
                        <ul class="space-y-2 text-sm">
                            <li><RouterLink to="/productos" class="hover:text-white transition-colors">Todos los productos</RouterLink></li>
                            <li><RouterLink to="/buscar" class="hover:text-white transition-colors">Buscar</RouterLink></li>
                            <li><RouterLink to="/carrito" class="hover:text-white transition-colors">Carrito</RouterLink></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-stone-800 mt-12 pt-8 text-xs text-center">
                    © {{ new Date().getFullYear() }} Tienda Virtual. Todos los derechos reservados.
                </div>
            </div>
        </footer>

        <CartDrawer :open="cartOpen" @close="cartOpen = false" />
        <MobileMenu :open="menuOpen" :categories="categories" @close="menuOpen = false" />
    </div>
</template>

<style scoped>
.search-enter-active, .search-leave-active { transition: all 0.2s ease; }
.search-enter-from, .search-leave-to { opacity: 0; transform: translateY(-8px); }
</style>
