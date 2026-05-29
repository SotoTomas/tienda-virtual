<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: AdminLayout })

const mainImagePreview   = ref(null)
const galleryPreviews    = ref([])

function onMainImageChange(e) {
    const file = e.target.files[0]
    if (!file) return
    form.main_image = file
    mainImagePreview.value = URL.createObjectURL(file)
}

function onGalleryChange(e) {
    const files = Array.from(e.target.files)
    form.gallery = files
    galleryPreviews.value = files.map(f => URL.createObjectURL(f))
}

const props = defineProps({
    categories: Array,
})

const form = useForm({
    name:              '',
    slug:              '',
    category_id:       '',
    short_description: '',
    description:       '',
    price:             '',
    compare_price:     '',
    sku:               '',
    stock:             0,
    manage_stock:      true,
    is_active:         true,
    is_featured:       false,
    weight:            '',
    main_image:       null,
    gallery:          [],
})

// Auto-genera el slug desde el nombre
function generateSlug() {
    form.slug = form.name
        .toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim()
}

function submit() {
    form.post(route('admin.products.store'),{
        forceFormData: true,
    })
}
</script>

<template>
    <div>
        <div class="flex items-center gap-4 mb-8">
            <Link :href="route('admin.products.index')" class="text-stone-400 hover:text-stone-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
            </Link>
            <h1 class="font-display text-3xl text-stone-900">Nuevo producto</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Formulario principal -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Info básica -->
                <div class="bg-white border border-stone-100 rounded p-6 space-y-5">
                    <h2 class="font-medium text-stone-800">Información básica</h2>

                    <div>
                        <label class="block text-xs text-stone-500 mb-1.5">Nombre *</label>
                        <input v-model="form.name" @input="generateSlug" type="text" class="input-base"
                            :class="form.errors.name ? 'border-red-400' : ''"/>
                        <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-xs text-stone-500 mb-1.5">Slug *</label>
                        <input v-model="form.slug" type="text" class="input-base font-mono text-sm"
                            :class="form.errors.slug ? 'border-red-400' : ''"/>
                        <p v-if="form.errors.slug" class="text-xs text-red-500 mt-1">{{ form.errors.slug }}</p>
                    </div>

                    <div>
                        <label class="block text-xs text-stone-500 mb-1.5">Categoría *</label>
                        <select v-model="form.category_id" class="input-base"
                            :class="form.errors.category_id ? 'border-red-400' : ''">
                            <option value="">Seleccioná una categoría</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.category_id" class="text-xs text-red-500 mt-1">{{ form.errors.category_id }}</p>
                    </div>

                    <div>
                        <label class="block text-xs text-stone-500 mb-1.5">Descripción corta</label>
                        <textarea v-model="form.short_description" rows="2" class="input-base resize-none"/>
                    </div>

                    <div>
                        <label class="block text-xs text-stone-500 mb-1.5">Descripción completa</label>
                        <textarea v-model="form.description" rows="5" class="input-base resize-none"/>
                    </div>
                </div>

                <!-- Precios -->
                <div class="bg-white border border-stone-100 rounded p-6 space-y-5">
                    <h2 class="font-medium text-stone-800">Precios</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-stone-500 mb-1.5">Precio *</label>
                            <input v-model="form.price" type="number" step="0.01" class="input-base"
                                :class="form.errors.price ? 'border-red-400' : ''"/>
                            <p v-if="form.errors.price" class="text-xs text-red-500 mt-1">{{ form.errors.price }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-stone-500 mb-1.5">Precio original (tachado)</label>
                            <input v-model="form.compare_price" type="number" step="0.01" class="input-base"/>
                        </div>
                    </div>
                </div>

                <!-- Inventario -->
                <div class="bg-white border border-stone-100 rounded p-6 space-y-5">
                    <h2 class="font-medium text-stone-800">Inventario</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-stone-500 mb-1.5">SKU</label>
                            <input v-model="form.sku" type="text" class="input-base font-mono text-sm"/>
                        </div>
                        <div>
                            <label class="block text-xs text-stone-500 mb-1.5">Stock</label>
                            <input v-model="form.stock" type="number" class="input-base"/>
                        </div>
                        <div>
                            <label class="block text-xs text-stone-500 mb-1.5">Peso (kg)</label>
                            <input v-model="form.weight" type="number" step="0.01" class="input-base"/>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <input v-model="form.manage_stock" type="checkbox" id="manage_stock" class="w-4 h-4 accent-stone-900"/>
                        <label for="manage_stock" class="text-sm text-stone-600">Controlar stock</label>
                    </div>
                </div>
                <!-- Imágenes-->
                <div class="bg-white border border-stone-100 rounded p-6 space-y-5">
                    <h2 class="font-medium text-stone-800">Imágenes</h2>

                    <!-- Imagen principal -->
                    <div>
                        <label class="block text-xs text-stone-500 mb-1.5">Imagen principal</label>
                        <div class="flex items-start gap-4">
                            <div v-if="mainImagePreview"
                                class="w-24 h-24 bg-stone-100 overflow-hidden shrink-0">
                                <img :src="mainImagePreview" class="w-full h-full object-cover"/>
                            </div>
                            <div v-else class="w-24 h-24 bg-stone-100 flex items-center justify-center shrink-0 text-stone-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <input
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    @change="onMainImageChange"
                                    class="block w-full text-sm text-stone-500
                                        file:mr-4 file:py-2 file:px-4 file:border-0
                                        file:text-xs file:font-medium file:tracking-wide
                                        file:bg-stone-900 file:text-white
                                        hover:file:bg-brand-600 cursor-pointer"
                                />
                                <p class="text-xs text-stone-400 mt-1.5">JPG, PNG o WebP. Máximo 2MB.</p>
                                <p v-if="form.errors.main_image" class="text-xs text-red-500 mt-1">{{ form.errors.main_image }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Galería -->
                    <div>
                        <label class="block text-xs text-stone-500 mb-1.5">Galería (múltiples imágenes)</label>
                        <input
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            multiple
                            @change="onGalleryChange"
                            class="block w-full text-sm text-stone-500
                                file:mr-4 file:py-2 file:px-4 file:border-0
                                file:text-xs file:font-medium file:tracking-wide
                                file:bg-stone-100 file:text-stone-700
                                hover:file:bg-stone-200 cursor-pointer"
                        />
                        <p class="text-xs text-stone-400 mt-1.5">Podés seleccionar múltiples archivos.</p>

                        <!-- Previews galería -->
                        <div v-if="galleryPreviews.length" class="flex flex-wrap gap-3 mt-3">
                            <div v-for="(src, i) in galleryPreviews" :key="i"
                                class="w-20 h-20 bg-stone-100 overflow-hidden">
                                <img :src="src" class="w-full h-full object-cover"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar opciones -->
            <div class="space-y-6">

                <!-- Publicar -->
                <div class="bg-white border border-stone-100 rounded p-6 space-y-4">
                    <h2 class="font-medium text-stone-800">Publicación</h2>
                    <div class="flex items-center justify-between">
                        <label class="text-sm text-stone-600">Activo</label>
                        <button @click="form.is_active = !form.is_active"
                            class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors"
                            :class="form.is_active ? 'bg-green-500' : 'bg-stone-200'">
                            <span class="inline-block h-3.5 w-3.5 rounded-full bg-white shadow transform transition-transform"
                                :class="form.is_active ? 'translate-x-4.5' : 'translate-x-0.5'"/>
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="text-sm text-stone-600">Destacado</label>
                        <button @click="form.is_featured = !form.is_featured"
                            class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors"
                            :class="form.is_featured ? 'bg-brand-500' : 'bg-stone-200'">
                            <span class="inline-block h-3.5 w-3.5 rounded-full bg-white shadow transform transition-transform"
                                :class="form.is_featured ? 'translate-x-4.5' : 'translate-x-0.5'"/>
                        </button>
                    </div>
                    <button @click="submit" :disabled="form.processing"
                        class="btn-primary w-full py-3 text-sm tracking-wide disabled:opacity-50">
                        {{ form.processing ? 'Guardando...' : 'Crear producto' }}
                    </button>
                    <Link :href="route('admin.products.index')" class="btn-secondary w-full py-2.5 text-sm text-center block">
                        Cancelar
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
