<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { productImage } from '@/utils/catalog'

const props = defineProps({
    product: Object,
})

const cart = useCartStore()
const adding = ref(false)

async function addToCart() {
    adding.value = true
    await cart.addItem(props.product.id, 1)
    adding.value = false
}
</script>

<template>
    <article class="group relative flex flex-col">

        <div class="relative aspect-[3/4] bg-stone-100 overflow-hidden">
            <RouterLink :to="`/productos/${product.slug}`">
                <img
                    v-if="product.main_image"
                    :src="productImage(product.main_image)"
                    :alt="product.name"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                />
                <div v-else class="w-full h-full flex items-center justify-center text-stone-300">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z"/>
                    </svg>
                </div>
            </RouterLink>

            <span v-if="product.has_discount"
                class="badge absolute top-3 left-3 bg-brand-500 text-white">
                -{{ product.discount_percentage }}%
            </span>

            <span v-if="!product.is_in_stock"
                class="badge absolute top-3 left-3 bg-stone-700 text-white">
                Sin stock
            </span>

            <div class="absolute bottom-0 left-0 right-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                <button
                    @click="addToCart"
                    :disabled="!product.is_in_stock || adding"
                    class="w-full py-3 bg-stone-900 text-white text-xs font-medium tracking-widest uppercase
                           hover:bg-brand-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ adding ? 'Agregando...' : 'Agregar al carrito' }}
                </button>
            </div>
        </div>

        <div class="mt-3 flex flex-col gap-1">
            <span v-if="product.category" class="text-[11px] text-stone-400 tracking-widest uppercase">
                {{ product.category }}
            </span>
            <RouterLink :to="`/productos/${product.slug}`"
                class="text-sm font-medium text-stone-800 hover:text-brand-600 transition-colors line-clamp-1">
                {{ product.name }}
            </RouterLink>
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-stone-900">
                    ${{ Number(product.price).toLocaleString('es-AR') }}
                </span>
                <span v-if="product.compare_price"
                    class="text-xs text-stone-400 line-through">
                    ${{ Number(product.compare_price).toLocaleString('es-AR') }}
                </span>
            </div>
        </div>
    </article>
</template>
