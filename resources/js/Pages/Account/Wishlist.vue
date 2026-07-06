<script setup>
import StoreLayout from '@/Layouts/StoreLayout.vue'
import ProductCard from '@/Components/Product/ProductCard.vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: StoreLayout })

defineProps({
    items: Array,
})

function remove(productId) {
    router.post(route('wishlist.toggle', productId), {}, { preserveScroll: true })
}
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="font-display text-4xl text-stone-900 mb-10">Lista de deseos</h1>

        <div v-if="items?.length"
            class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-10">
            <div v-for="item in items" :key="item.id" class="relative group">
                <button
                    @click="remove(item.product.id)"
                    class="absolute top-2 right-2 z-10 w-7 h-7 bg-white rounded-full shadow flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:text-red-500"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <ProductCard :product="item.product" />
            </div>
        </div>

        <div v-else class="flex flex-col items-center justify-center py-32 text-stone-400">
            <svg class="w-20 h-20 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
            </svg>
            <p class="font-display text-2xl text-stone-600 mb-2">Tu lista está vacía</p>
            <p class="text-sm mb-8">Guardá los productos que te gustan para comprarlos después.</p>
            <a :href="route('products.index')" class="btn-primary px-8 py-3 text-xs tracking-widest uppercase">
                Ver productos
            </a>
        </div>
    </div>
</template>
