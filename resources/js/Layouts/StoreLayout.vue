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
        <header class="sticky top-0 z-40 bg-stone-50/95 backdrop-blur-sm border-b border-stone-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <Link :href="route('store.home')" class="font-display text-2xl text-stone-900">
                        Tienda<span class="text-brand-500">.</span>
                    </Link>
                    <button @click="cartOpen = !cartOpen" class="relative p-2">
                        🛒
                        <span v-if="cartCount > 0" class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-brand-500 text-white text-[10px] rounded-full flex items-center justify-center">
                            {{ cartCount }}
                        </span>
                    </button>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>
    </div>
</template>