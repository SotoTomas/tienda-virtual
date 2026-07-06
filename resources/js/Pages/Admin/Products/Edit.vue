<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, router, Link } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    product:    Object,
    categories: Array,
})

const form = useForm({
    name:              props.product.name,
    slug:              props.product.slug,
    category_id:       props.product.category_id,
    short_description: props.product.short_description ?? '',
    description:       props.product.description ?? '',
    price:             props.product.price,
    compare_price:     props.product.compare_price ?? '',
    sku:               props.product.sku ?? '',
    stock:             props.product.stock,
    manage_stock:      props.product.manage_stock,
    is_active:         props.product.is_active,
    is_featured:       props.product.is_featured,
    weight:            props.product.weight ?? '',
    main_image:       null,
    gallery:          [],
})
// Previsualización de imágenes
const mainImagePreview = ref(null)
const galleryPreviews  = ref([])
const deletingImage    = ref(null)

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

function deleteGalleryImage(path) {
    router.patch(route('admin.products.update', props.product.id), {
        delete_gallery_image: path,
    }, { preserveScroll: true })
}

function generateSlug() {
    form.slug = form.name
        .toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .trim()
}

function submit() {
    router.post(route('admin.products.update', props.product.id), {
        ...form.data(),
        _method: 'PATCH',
    }, {
        forceFormData: true,
        onSuccess: () => {
            // éxito
        },
        onError: (errors) => {
            form.setError(errors)
        },
    })
}

// ── Variantes ────────────────────────────────────────────
const showVariantForm = ref(false)
const editingVariant  = ref(null)

const variantForm = useForm({
    name:      '',
    sku:       '',
    price:     '',
    stock:     0,
    options:   {},
    is_active: true,

})

const optionKeys   = ref(['color', 'talle'])
const optionValues = ref({ color: '', talle: '' })

function buildOptions() {
    const opts = {}
    optionKeys.value.forEach(key => {
        if (optionValues.value[key]) opts[key] = optionValues.value[key]
    })
    variantForm.options = opts

    // Auto-generar nombre del variante
    variantForm.name = Object.values(opts).filter(Boolean).join(' - ')
}

function editVariant(variant) {
    editingVariant.value    = variant.id
    variantForm.name        = variant.name
    variantForm.sku         = variant.sku ?? ''
    variantForm.price       = variant.price ?? ''
    variantForm.stock       = variant.stock
    variantForm.options     = variant.options
    variantForm.is_active   = variant.is_active
    optionValues.value      = { ...variant.options }
    showVariantForm.value   = true
}

function submitVariant() {
    buildOptions()
    if (editingVariant.value) {
        variantForm.patch(
            route('admin.products.variants.update', [props.product.id, editingVariant.value]),
            { onSuccess: () => { showVariantForm.value = false; editingVariant.value = null; variantForm.reset() } }
        )
    } else {
        variantForm.post(
            route('admin.products.variants.store', props.product.id),
            { onSuccess: () => { showVariantForm.value = false; variantForm.reset() } }
        )
    }
}

function destroyVariant(variantId) {
    if (!confirm('¿Eliminar esta variante?')) return
    router.delete(route('admin.products.variants.destroy', [props.product.id, variantId]), {
        preserveScroll: true,
    })
}

function cancelVariant() {
    showVariantForm.value = false
    editingVariant.value  = null
    variantForm.reset()
    optionValues.value    = { color: '', talle: '' }
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
            <h1 class="font-display text-3xl text-stone-900">Editar producto</h1>
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
                    </div>

                    <div>
                        <label class="block text-xs text-stone-500 mb-1.5">Categoría *</label>
                        <select v-model="form.category_id" class="input-base">
                            <option value="">Seleccioná una categoría</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
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
                <!-- Imágenes -->
                <div class="bg-white border border-stone-100 rounded p-6 space-y-5">
                    <h2 class="font-medium text-stone-800">Imágenes</h2>

                    <!-- Imagen principal actual -->
                    <div>
                        <label class="block text-xs text-stone-500 mb-1.5">Imagen principal</label>
                        <div class="flex items-start gap-4">
                            <div class="w-24 h-24 bg-stone-100 overflow-hidden shrink-0">
                                <img
                                    v-if="mainImagePreview || product.main_image"
                                    :src="mainImagePreview ?? `/${product.main_image}`"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center text-stone-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <input
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    @change="onMainImageChange"
                                    class="block w-full text-sm text-stone-500
                                        file:mr-4 file:py-2 file:px-4 file:border-0
                                        file:text-xs file:font-medium
                                        file:bg-stone-900 file:text-white
                                        hover:file:bg-brand-600 cursor-pointer"
                                />
                                <p class="text-xs text-stone-400 mt-1.5">Reemplaza la imagen actual. JPG, PNG o WebP, máx 2MB.</p>
                                <p v-if="form.errors.main_image" class="text-xs text-red-500 mt-1">{{ form.errors.main_image }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Galería actual -->
                    <div v-if="product.gallery?.length">
                        <label class="block text-xs text-stone-500 mb-2">Galería actual</label>
                        <div class="flex flex-wrap gap-3">
                            <div v-for="img in product.gallery" :key="img"
                                class="relative group w-20 h-20 bg-stone-100 overflow-hidden">
                                <img :src="`/${img}`" class="w-full h-full object-cover"/>
                                <button
                                    @click="deleteGalleryImage(img)"
                                    class="absolute inset-0 bg-red-500/70 text-white opacity-0 group-hover:opacity-100
                                        transition-opacity flex items-center justify-center text-xs"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Agregar a galería -->
                    <div>
                        <label class="block text-xs text-stone-500 mb-1.5">Agregar imágenes a la galería</label>
                        <input
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            multiple
                            @change="onGalleryChange"
                            class="block w-full text-sm text-stone-500
                                file:mr-4 file:py-2 file:px-4 file:border-0
                                file:text-xs file:font-medium
                                file:bg-stone-100 file:text-stone-700
                                hover:file:bg-stone-200 cursor-pointer"
                        />

                        <div v-if="galleryPreviews.length" class="flex flex-wrap gap-3 mt-3">
                            <div v-for="(src, i) in galleryPreviews" :key="i"
                                class="w-20 h-20 bg-stone-100 overflow-hidden">
                                <img :src="src" class="w-full h-full object-cover"/>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Variantes -->
                <div class="bg-white border border-stone-100 rounded p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="font-medium text-stone-800">Variantes</h2>
                        <button @click="showVariantForm = true; editingVariant = null; variantForm.reset()"
                            class="text-xs btn-secondary px-4 py-2">
                            + Agregar variante
                        </button>
                    </div>

                    <!-- Formulario variante -->
                    <div v-if="showVariantForm" class="bg-stone-50 border border-stone-200 p-5 mb-6 space-y-4">
                        <h3 class="text-sm font-medium text-stone-700">
                            {{ editingVariant ? 'Editar variante' : 'Nueva variante' }}
                        </h3>

                        <!-- Opciones (color, talle) -->
                        <div class="grid grid-cols-2 gap-3">
                            <div v-for="key in optionKeys" :key="key">
                                <label class="block text-xs text-stone-500 mb-1.5 capitalize">{{ key }}</label>
                                <input
                                    v-model="optionValues[key]"
                                    :placeholder="key === 'color' ? 'Ej: Rojo' : 'Ej: M'"
                                    type="text"
                                    class="input-base text-sm"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs text-stone-500 mb-1.5">SKU</label>
                                <input v-model="variantForm.sku" type="text" class="input-base text-sm font-mono"/>
                            </div>
                            <div>
                                <label class="block text-xs text-stone-500 mb-1.5">Precio (opcional)</label>
                                <input v-model="variantForm.price" type="number" step="0.01" class="input-base text-sm"
                                    placeholder="Igual al base"/>
                            </div>
                            <div>
                                <label class="block text-xs text-stone-500 mb-1.5">Stock *</label>
                                <input v-model="variantForm.stock" type="number" class="input-base text-sm"/>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <input v-model="variantForm.is_active" type="checkbox" id="var_active" class="w-4 h-4 accent-stone-900"/>
                            <label for="var_active" class="text-sm text-stone-600">Activa</label>
                        </div>

                        <div class="flex gap-3">
                            <button @click="submitVariant" :disabled="variantForm.processing"
                                class="btn-primary px-5 py-2 text-xs disabled:opacity-50">
                                {{ variantForm.processing ? 'Guardando...' : editingVariant ? 'Actualizar' : 'Agregar' }}
                            </button>
                            <button @click="cancelVariant" class="btn-secondary px-5 py-2 text-xs">
                                Cancelar
                            </button>
                        </div>
                    </div>

                    <!-- Lista variantes -->
                    <div v-if="product.variants?.length" class="space-y-2">
                        <div v-for="variant in product.variants" :key="variant.id"
                            class="flex items-center justify-between py-3 border-b border-stone-50 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-stone-800">{{ variant.name }}</p>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-xs text-stone-400">
                                        Stock: <span :class="variant.stock <= 3 ? 'text-red-500 font-medium' : 'text-stone-600'">{{ variant.stock }}</span>
                                    </span>
                                    <span v-if="variant.price" class="text-xs text-stone-400">
                                        ${{ Number(variant.price).toLocaleString('es-AR') }}
                                    </span>
                                    <span v-if="variant.sku" class="text-xs text-stone-400 font-mono">{{ variant.sku }}</span>
                                    <span class="text-xs" :class="variant.is_active ? 'text-green-500' : 'text-stone-300'">
                                        {{ variant.is_active ? '● Activa' : '● Inactiva' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button @click="editVariant(variant)"
                                    class="text-xs text-stone-400 hover:text-stone-700 transition-colors">
                                    Editar
                                </button>
                                <button @click="destroyVariant(variant.id)"
                                    class="text-xs text-stone-400 hover:text-red-500 transition-colors">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-else class="py-8 text-center text-stone-400 text-sm">
                        Este producto no tiene variantes todavía.
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
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

                    <!-- Imagen actual -->
                    <div v-if="product.main_image" class="pt-2">
                        <p class="text-xs text-stone-500 mb-2">Imagen principal</p>
                        <img :src="`/${product.main_image}`" :alt="product.name"
                            class="w-full aspect-square object-cover bg-stone-100"/>
                    </div>

                    <button @click="submit" :disabled="form.processing"
                        class="btn-primary w-full py-3 text-sm tracking-wide disabled:opacity-50">
                        {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
                    </button>
                    <Link :href="route('admin.products.index')" class="btn-secondary w-full py-2.5 text-sm text-center block">
                        Cancelar
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
