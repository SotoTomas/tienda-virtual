<script setup>
import StoreLayout from '@/Layouts/StoreLayout.vue'
import ProductCard from '@/Components/Product/ProductCard.vue'
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: StoreLayout })

const props = defineProps({
    query:    String,
    products: Object,
})

const localQuery = ref(props.query ?? '')

watch(localQuery, (val) => {
    if (val.length >= 2 || val.length === 0) {
        router.get(route('search'), { q: val }, { preserveScroll: true, replace: true })
    }
})
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-xl mb-10">
            <h1 class="font-display text-4xl text-stone-900 mb-6">Buscar</h1>
            <div class="relative">
                <input
                    v-model="localQuery"
                    type="text"
                    placeholder="¿Qué estás buscando?"
                    class="input-base pr-12 text-base"
                    autofocus
                />
                <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-stone-400"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                </svg>
            </div>
        </div>

        <!-- Resultados -->
        <div v-if="query?.length >= 2">
            <p class="text-sm text-stone-500 mb-8">
                <span v-if="products.total > 0">
                    {{ products.total }} resultados para "<span class="text-stone-800 font-medium">{{ query }}</span>"
                </span>
                <span v-else>
                    Sin resultados para "<span class="text-stone-800 font-medium">{{ query }}</span>"
                </span>
            </p>

            <div v-if="products.data?.length"
                class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-10">
                <ProductCard
                    v-for="product in products.data"
                    :key="product.id"
                    :product="product"
                />
            </div>

            <div v-else class="py-16 text-center text-stone-400">
                <p class="text-sm">Probá con otros términos o explorá el catálogo.</p>
            </div>
        </div>

        <div v-else class="py-16 text-center text-stone-300">
            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
            </svg>
            <p class="text-sm">Escribí al menos 2 caracteres para buscar.</p>
        </div>
    </div>
</template>
