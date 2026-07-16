<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import ProductCard from '@/Components/Product/ProductCard.vue'
import { useCatalogStore } from '@/stores/catalog'
import { useCartStore } from '@/stores/cart'
import { productImage } from '@/utils/catalog'

const route = useRoute()
const catalog = useCatalogStore()
const cart = useCartStore()

const product = ref(null)
const related = ref([])
const selectedVariant = ref(null)
const selectedImage = ref(null)
const quantity = ref(1)
const adding = ref(false)
const activeTab = ref('description')

const effectivePrice = computed(() => {
    if (selectedVariant.value?.price) return selectedVariant.value.price
    return product.value?.price ?? 0
})

const isInStock = computed(() => {
    if (selectedVariant.value) return selectedVariant.value.in_stock
    return product.value?.is_in_stock ?? false
})

const variantOptions = computed(() => {
    const options = {}
    product.value?.variants?.forEach((v) => {
        Object.entries(v.options).forEach(([key, value]) => {
            if (!options[key]) options[key] = new Set()
            options[key].add(value)
        })
    })
    return Object.fromEntries(Object.entries(options).map(([k, v]) => [k, [...v]]))
})

const selectedOptions = ref({})

function selectOption(key, value) {
    selectedOptions.value = { ...selectedOptions.value, [key]: value }
    selectedVariant.value =
        product.value?.variants?.find((v) =>
            Object.entries(selectedOptions.value).every(([k, val]) => v.options[k] === val),
        ) ?? null
}

function isOptionSelected(key, value) {
    return selectedOptions.value[key] === value
}

async function loadProduct() {
    const data = await catalog.getProductBySlug(route.params.slug)
    product.value = data
    selectedImage.value = data?.main_image ?? null
    selectedVariant.value = null
    selectedOptions.value = {}

    if (data) {
        related.value = await catalog.getRelatedProducts(data.category.slug, data.id)
    }
}

async function addToCart() {
    if (!isInStock.value || !product.value) return
    adding.value = true
    await cart.addItem(
        product.value.id,
        quantity.value,
        selectedVariant.value?.id ?? null,
    )
    adding.value = false
}

watch(() => route.params.slug, loadProduct)

onMounted(loadProduct)
</script>

<template>
    <div v-if="product" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-stone-400 mb-8">
            <RouterLink to="/" class="hover:text-stone-700">Inicio</RouterLink>
            <span>/</span>
            <RouterLink :to="`/categoria/${product.category.slug}`" class="hover:text-stone-700">
                {{ product.category.name }}
            </RouterLink>
            <span>/</span>
            <span class="text-stone-600">{{ product.name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">

            <!-- Galería -->
            <div class="space-y-4">
                <div class="aspect-[4/5] bg-stone-100 overflow-hidden">
                    <img
                        v-if="selectedImage"
                        :src="productImage(selectedImage)"
                        :alt="product.name"
                        class="w-full h-full object-cover"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center text-stone-300">
                        <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z"/>
                        </svg>
                    </div>
                </div>

                <div v-if="product.images?.length" class="flex gap-3 overflow-x-auto pb-1">
                    <button
                        v-for="img in product.images"
                        :key="img.path"
                        @click="selectedImage = img.path"
                        class="w-20 h-20 shrink-0 bg-stone-100 overflow-hidden border-2 transition-colors"
                        :class="selectedImage === img.path ? 'border-stone-900' : 'border-transparent'"
                    >
                        <img :src="productImage(img.path)" :alt="img.alt" class="w-full h-full object-cover"/>
                    </button>
                </div>
            </div>

            <!-- Info del producto -->
            <div class="flex flex-col">
                <p class="text-xs text-stone-400 tracking-widest uppercase mb-2">
                    {{ product.category.name }}
                </p>
                <h1 class="font-display text-4xl text-stone-900 leading-tight">
                    {{ product.name }}
                </h1>

                <div v-if="product.reviews?.length" class="flex items-center gap-2 mt-3">
                    <div class="flex">
                        <span v-for="i in 5" :key="i" class="text-sm"
                            :class="i <= product.average_rating ? 'text-brand-500' : 'text-stone-200'">
                            ★
                        </span>
                    </div>
                    <span class="text-xs text-stone-400">
                        {{ product.average_rating }} ({{ product.reviews.length }} reseñas)
                    </span>
                </div>

                <div class="flex items-baseline gap-3 mt-6">
                    <span class="font-display text-3xl text-stone-900">
                        ${{ Number(effectivePrice).toLocaleString('es-AR') }}
                    </span>
                    <span v-if="product.compare_price" class="text-lg text-stone-400 line-through">
                        ${{ Number(product.compare_price).toLocaleString('es-AR') }}
                    </span>
                    <span v-if="product.has_discount"
                        class="badge bg-brand-500 text-white text-xs">
                        -{{ product.discount_percentage }}%
                    </span>
                </div>

                <p class="mt-4 text-stone-600 text-sm leading-relaxed">
                    {{ product.short_description }}
                </p>

                <div class="border-t border-stone-100 mt-6 pt-6 space-y-6">

                    <div v-for="(values, key) in variantOptions" :key="key">
                        <h3 class="text-xs font-medium tracking-widest uppercase text-stone-500 mb-3">
                            {{ key }}
                            <span v-if="selectedOptions[key]" class="text-stone-900 normal-case tracking-normal ml-1">
                                — {{ selectedOptions[key] }}
                            </span>
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="value in values"
                                :key="value"
                                @click="selectOption(key, value)"
                                class="px-4 py-2 text-sm border transition-colors"
                                :class="isOptionSelected(key, value)
                                    ? 'border-stone-900 bg-stone-900 text-white'
                                    : 'border-stone-200 text-stone-700 hover:border-stone-900'"
                            >
                                {{ value }}
                            </button>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-medium tracking-widest uppercase text-stone-500 mb-3">Cantidad</h3>
                        <div class="flex items-center border border-stone-200 w-fit">
                            <button @click="quantity = Math.max(1, quantity - 1)"
                                class="w-10 h-10 flex items-center justify-center text-stone-500 hover:text-stone-900 text-lg">
                                −
                            </button>
                            <span class="w-12 text-center text-sm font-medium">{{ quantity }}</span>
                            <button @click="quantity = Math.min(10, quantity + 1)"
                                class="w-10 h-10 flex items-center justify-center text-stone-500 hover:text-stone-900 text-lg">
                                +
                            </button>
                        </div>
                    </div>

                    <p class="text-xs flex items-center gap-1.5"
                        :class="isInStock ? 'text-green-600' : 'text-red-500'">
                        <span class="w-1.5 h-1.5 rounded-full"
                            :class="isInStock ? 'bg-green-500' : 'bg-red-400'"/>
                        {{ isInStock ? 'En stock' : 'Sin stock' }}
                    </p>

                    <button
                        @click="addToCart"
                        :disabled="!isInStock || adding"
                        class="btn-primary w-full py-4 tracking-widest uppercase text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ adding ? 'Agregando...' : 'Agregar al carrito' }}
                    </button>

                    <p v-if="product.sku" class="text-xs text-stone-400">
                        SKU: {{ product.sku }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mt-20 border-t border-stone-200">
            <div class="flex gap-8 border-b border-stone-200">
                <button
                    v-for="tab in ['description', 'reviews']"
                    :key="tab"
                    @click="activeTab = tab"
                    class="py-4 text-sm font-medium border-b-2 -mb-px transition-colors"
                    :class="activeTab === tab
                        ? 'border-stone-900 text-stone-900'
                        : 'border-transparent text-stone-400 hover:text-stone-700'"
                >
                    {{ tab === 'description' ? 'Descripción' : `Reseñas (${product.reviews?.length ?? 0})` }}
                </button>
            </div>

            <div v-if="activeTab === 'description'" class="py-8 max-w-2xl">
                <p class="text-stone-600 leading-relaxed whitespace-pre-line">
                    {{ product.description }}
                </p>
            </div>

            <div v-if="activeTab === 'reviews'" class="py-8">
                <div v-if="product.reviews?.length" class="space-y-6 max-w-2xl">
                    <div v-for="review in product.reviews" :key="review.id"
                        class="border-b border-stone-100 pb-6 last:border-0">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <span class="text-sm font-medium text-stone-800">{{ review.user_name }}</span>
                                <div class="flex mt-1">
                                    <span v-for="i in 5" :key="i" class="text-xs"
                                        :class="i <= review.rating ? 'text-brand-500' : 'text-stone-200'">
                                        ★
                                    </span>
                                </div>
                            </div>
                            <span class="text-xs text-stone-400">{{ review.created_at }}</span>
                        </div>
                        <p v-if="review.title" class="text-sm font-medium text-stone-700 mb-1">{{ review.title }}</p>
                        <p class="text-sm text-stone-600">{{ review.body }}</p>
                    </div>
                </div>
                <div v-else class="py-12 text-center text-stone-400">
                    <p class="text-sm">Todavía no hay reseñas para este producto.</p>
                </div>
            </div>
        </div>

        <div v-if="related?.length" class="mt-20">
            <h2 class="font-display text-3xl text-stone-900 mb-8">También te puede gustar</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-10">
                <ProductCard
                    v-for="item in related"
                    :key="item.id"
                    :product="item"
                />
            </div>
        </div>
    </div>
</template>
