<script setup>
import StoreLayout from '@/Layouts/StoreLayout.vue'
import ProductCard from '@/Components/Product/ProductCard.vue'
import { Link } from '@inertiajs/vue3'

defineOptions({ layout: StoreLayout })

defineProps({
    category: Object,
    products: Object,
})
</script>

<template>
    <div>
        <!-- Hero categoría -->
        <div class="bg-stone-900 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4">
                    <Link :href="route('store.home')" class="hover:text-stone-300">Inicio</Link>
                    <span>/</span>
                    <span class="text-stone-300">{{ category.name }}</span>
                </nav>
                <h1 class="font-display text-4xl lg:text-5xl text-white">{{ category.name }}</h1>
                <p v-if="category.description" class="mt-3 text-stone-400 max-w-xl">
                    {{ category.description }}
                </p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Subcategorías -->
            <div v-if="category.children?.length" class="flex flex-wrap gap-2 mb-10">
                <Link
                    v-for="child in category.children"
                    :key="child.id"
                    :href="route('categories.show', child.slug)"
                    class="px-4 py-2 border border-stone-200 text-sm text-stone-600 hover:border-stone-900 hover:text-stone-900 transition-colors"
                >
                    {{ child.name }}
                </Link>
            </div>

            <!-- Grid -->
            <div v-if="products.data?.length"
                class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-10">
                <ProductCard
                    v-for="product in products.data"
                    :key="product.id"
                    :product="product"
                />
            </div>

            <div v-else class="py-24 text-center text-stone-400">
                <p class="text-sm">No hay productos en esta categoría por el momento.</p>
            </div>

            <!-- Paginación -->
            <div v-if="products.last_page > 1" class="mt-12 flex justify-center gap-2">
                <Link
                    v-for="link in products.links"
                    :key="link.label"
                    :href="link.url ?? '#'"
                    v-html="link.label"
                    class="px-3 py-2 text-sm transition-colors"
                    :class="[
                        link.active ? 'bg-stone-900 text-white' : 'text-stone-500 hover:text-stone-900',
                        !link.url ? 'opacity-30 pointer-events-none' : ''
                    ]"
                />
            </div>
        </div>
    </div>
</template>