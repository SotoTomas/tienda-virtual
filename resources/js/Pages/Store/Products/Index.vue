<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import ProductCard from '@/Components/Product/ProductCard.vue'
import { useCatalogStore } from '@/stores/catalog'

const route = useRoute()
const router = useRouter()
const catalog = useCatalogStore()

const products = ref({ data: [], total: 0, last_page: 1 })
const categories = ref([])
const filters = ref({})

const localFilters = ref({
    categoria: '',
    precio_min: '',
    precio_max: '',
    buscar: '',
    orden: 'recientes',
    destacados: '',
})

async function loadProducts() {
    const query = route.query
    filters.value = { ...query }
    localFilters.value = {
        categoria: query.categoria ?? '',
        precio_min: query.precio_min ?? '',
        precio_max: query.precio_max ?? '',
        buscar: query.buscar ?? '',
        orden: query.orden ?? 'recientes',
        destacados: query.destacados ?? '',
    }

    products.value = await catalog.getProducts(query, query.page ?? 1)
}

function applyFilters() {
    const query = { ...localFilters.value }
    Object.keys(query).forEach((key) => {
        if (!query[key]) delete query[key]
    })
    router.push({ name: 'products.index', query })
}

function clearFilters() {
    localFilters.value = {
        categoria: '',
        precio_min: '',
        precio_max: '',
        buscar: '',
        orden: 'recientes',
        destacados: '',
    }
    applyFilters()
}

function goToPage(page) {
    router.push({ name: 'products.index', query: { ...route.query, page } })
}

watch(() => route.query, loadProducts, { deep: true })

watch(
    () => localFilters.value.orden,
    (val, oldVal) => {
        if (oldVal !== undefined) applyFilters()
    },
)

onMounted(async () => {
    await catalog.ensureLoaded()
    categories.value = catalog.getFilterCategories()
    await loadProducts()
})
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Encabezado -->
        <div class="mb-8">
            <h1 class="font-display text-4xl text-stone-900">Catálogo</h1>
            <p class="text-stone-500 mt-2 text-sm">
                {{ products.total }} productos encontrados
            </p>
        </div>

        <div class="flex gap-8">

            <!-- ── Sidebar filtros ─────────────────────────── -->
            <aside class="hidden lg:block w-64 shrink-0">
                <div class="sticky top-24 space-y-8">

                    <!-- Categorías -->
                    <div>
                        <h3 class="text-xs font-medium tracking-widest uppercase text-stone-500 mb-4">Categoría</h3>
                        <ul class="space-y-2">
                            <li>
                                <button
                                    @click="localFilters.categoria = ''; applyFilters()"
                                    class="text-sm w-full text-left py-1 transition-colors"
                                    :class="!localFilters.categoria ? 'text-stone-900 font-medium' : 'text-stone-500 hover:text-stone-900'"
                                >
                                    Todas
                                </button>
                            </li>
                            <li v-for="cat in categories" :key="cat.id">
                                <button
                                    @click="localFilters.categoria = cat.slug; applyFilters()"
                                    class="text-sm w-full text-left py-1 transition-colors"
                                    :class="localFilters.categoria === cat.slug ? 'text-stone-900 font-medium' : 'text-stone-500 hover:text-stone-900'"
                                >
                                    {{ cat.name }}
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Precio -->
                    <div>
                        <h3 class="text-xs font-medium tracking-widest uppercase text-stone-500 mb-4">Precio</h3>
                        <div class="space-y-3">
                            <input
                                v-model="localFilters.precio_min"
                                type="number"
                                placeholder="Mínimo"
                                class="input-base text-sm"
                            />
                            <input
                                v-model="localFilters.precio_max"
                                type="number"
                                placeholder="Máximo"
                                class="input-base text-sm"
                            />
                            <button @click="applyFilters" class="btn-primary w-full py-2.5 text-xs tracking-widest uppercase">
                                Aplicar
                            </button>
                        </div>
                    </div>

                    <!-- Limpiar -->
                    <button
                        v-if="filters?.categoria || filters?.precio_min || filters?.precio_max"
                        @click="clearFilters"
                        class="text-xs text-stone-400 hover:text-red-500 transition-colors"
                    >
                        Limpiar filtros
                    </button>
                </div>
            </aside>

            <!-- ── Grid productos ──────────────────────────── -->
            <div class="flex-1 min-w-0">

                <!-- Toolbar -->
                <div class="flex items-center justify-between mb-6">
                    <span class="text-sm text-stone-500 lg:hidden">
                        {{ products.total }} resultados
                    </span>

                    <div class="flex items-center gap-3 ml-auto">
                        <label class="text-xs text-stone-500 hidden sm:block">Ordenar por</label>
                        <select
                            v-model="localFilters.orden"
                            class="input-base py-2 text-sm w-auto pr-8"
                        >
                            <option value="recientes">Más recientes</option>
                            <option value="precio_asc">Precio: menor a mayor</option>
                            <option value="precio_desc">Precio: mayor a menor</option>
                            <option value="nombre">Nombre A-Z</option>
                        </select>
                    </div>
                </div>

                <!-- Productos -->
                <div v-if="products.data?.length"
                    class="grid grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-10">
                    <ProductCard
                        v-for="product in products.data"
                        :key="product.id"
                        :product="product"
                    />
                </div>

                <!-- Sin resultados -->
                <div v-else class="flex flex-col items-center justify-center py-24 text-stone-400">
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/>
                    </svg>
                    <p class="text-sm">No encontramos productos con esos filtros.</p>
                    <button @click="clearFilters" class="mt-4 btn-secondary text-xs py-2 px-4">
                        Ver todos los productos
                    </button>
                </div>

                <!-- Paginación -->
                <div v-if="products.last_page > 1" class="mt-12 flex items-center justify-center gap-2">
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
    </div>
</template>
