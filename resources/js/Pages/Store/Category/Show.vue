<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import ProductCard from '@/Components/Product/ProductCard.vue'
import { useCatalogStore } from '@/stores/catalog'

const route = useRoute()
const router = useRouter()
const catalog = useCatalogStore()

const category = ref(null)
const products = ref({ data: [], total: 0, last_page: 1 })

async function loadCategory() {
    category.value = await catalog.getCategoryBySlug(route.params.slug)
    products.value = await catalog.getCategoryProducts(route.params.slug, route.query.page ?? 1)
}

function goToPage(page) {
    router.push({ name: 'categories.show', params: { slug: route.params.slug }, query: { page } })
}

watch(() => [route.params.slug, route.query.page], loadCategory)

onMounted(loadCategory)
</script>

<template>
    <div v-if="category">
        <div class="bg-stone-900 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center gap-2 text-xs text-stone-500 mb-4">
                    <RouterLink to="/" class="hover:text-stone-300">Inicio</RouterLink>
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
            <div v-if="category.children?.length" class="flex flex-wrap gap-2 mb-10">
                <RouterLink
                    v-for="child in category.children"
                    :key="child.id"
                    :to="`/categoria/${child.slug}`"
                    class="px-4 py-2 border border-stone-200 text-sm text-stone-600 hover:border-stone-900 hover:text-stone-900 transition-colors"
                >
                    {{ child.name }}
                </RouterLink>
            </div>

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

            <div v-if="products.last_page > 1" class="mt-12 flex justify-center gap-2">
                <button
                    v-for="page in products.last_page"
                    :key="page"
                    @click="goToPage(page)"
                    class="px-3 py-2 text-sm transition-colors"
                    :class="page === products.current_page
                        ? 'bg-stone-900 text-white'
                        : 'text-stone-500 hover:text-stone-900'"
                >
                    {{ page }}
                </button>
            </div>
        </div>
    </div>
</template>
