<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    products: Object,
    filters:  Object,
})

const search = ref(props.filters?.search ?? '')

function doSearch() {
    router.get(route('admin.products.index'), { search: search.value }, {
        preserveScroll: true, replace: true,
    })
}

function destroy(id) {
    if (!confirm('¿Eliminar este producto?')) return
    router.delete(route('admin.products.destroy', id), { preserveScroll: true })
}

function toggleActive(product) {
    router.patch(route('admin.products.update', product.id), {
        is_active: !product.is_active,
    }, { preserveScroll: true })
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="font-display text-3xl text-stone-900">Productos</h1>
                <p class="text-stone-500 text-sm mt-1">{{ products.total }} productos en total</p>
            </div>
            <Link :href="route('admin.products.create')" class="btn-primary px-5 py-2.5 text-sm">
                + Nuevo producto
            </Link>
        </div>

        <!-- Buscador -->
        <div class="bg-white border border-stone-100 rounded mb-6 px-4 py-3 flex gap-3">
            <input
                v-model="search"
                type="text"
                placeholder="Buscar por nombre o SKU..."
                class="input-base text-sm flex-1"
                @keyup.enter="doSearch"
            />
            <button @click="doSearch" class="btn-primary px-4 py-2 text-xs">Buscar</button>
        </div>

        <!-- Tabla -->
        <div class="bg-white border border-stone-100 rounded overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-stone-100 bg-stone-50">
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Producto</th>
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">SKU</th>
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Precio</th>
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Stock</th>
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Estado</th>
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Categoría</th>
                            <th class="px-6 py-3"/>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-50">
                        <tr v-for="product in products.data" :key="product.id"
                            class="hover:bg-stone-50 transition-colors">
                            <!-- Producto -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-stone-100 shrink-0 overflow-hidden rounded">
                                        <img v-if="product.main_image"
                                            :src="`/${product.main_image}`"
                                            class="w-full h-full object-cover"
                                        />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-stone-800">{{ product.name }}</p>
                                        <p class="text-xs text-stone-400">{{ product.variants_count }} variantes</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-stone-500 font-mono">{{ product.sku ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm font-medium">${{ Number(product.price).toLocaleString('es-AR') }}</td>
                            <td class="px-6 py-4">
                                <span class="text-sm"
                                    :class="product.stock <= 5 ? 'text-red-500 font-medium' : 'text-stone-700'">
                                    {{ product.stock }}
                                </span>
                            </td>
                            <!-- Toggle activo -->
                            <td class="px-6 py-4">
                                <button @click="toggleActive(product)"
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors"
                                    :class="product.is_active ? 'bg-green-500' : 'bg-stone-200'">
                                    <span class="inline-block h-3.5 w-3.5 rounded-full bg-white shadow transform transition-transform"
                                        :class="product.is_active ? 'translate-x-4.5' : 'translate-x-0.5'"/>
                                </button>
                            </td>
                            <td class="px-6 py-4 text-xs text-stone-500">{{ product.category }}</td>
                            <!-- Acciones -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <Link :href="route('admin.products.edit', product.id)"
                                        class="text-xs text-stone-400 hover:text-stone-700 transition-colors">
                                        Editar
                                    </Link>
                                    <button @click="destroy(product.id)"
                                        class="text-xs text-stone-400 hover:text-red-500 transition-colors">
                                        Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div v-if="products.last_page > 1" class="px-6 py-4 border-t border-stone-100 flex justify-center gap-2">
                <Link
                    v-for="link in products.links"
                    :key="link.label"
                    :href="link.url ?? '#'"
                    v-html="link.label"
                    class="px-3 py-1.5 text-xs transition-colors"
                    :class="[
                        link.active ? 'bg-stone-900 text-white' : 'text-stone-500 hover:text-stone-900',
                        !link.url ? 'opacity-30 pointer-events-none' : ''
                    ]"
                />
            </div>
        </div>
    </div>
</template>
