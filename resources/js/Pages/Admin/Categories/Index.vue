<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    categories: Array,
})

const showForm = ref(false)
const editing  = ref(null)

const form = useForm({
    name:      '',
    slug:      '',
    parent_id: null,
    is_active: true,
})

function generateSlug() {
    form.slug = form.name
        .toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .trim()
}

function edit(cat) {
    editing.value = cat.id
    form.name      = cat.name
    form.slug      = cat.slug
    form.parent_id = cat.parent_id
    form.is_active = cat.is_active
    showForm.value = true
}

function submit() {
    if (editing.value) {
        form.patch(route('admin.categories.update', editing.value), {
            onSuccess: () => { showForm.value = false; editing.value = null; form.reset() }
        })
    } else {
        form.post(route('admin.categories.store'), {
            onSuccess: () => { showForm.value = false; form.reset() }
        })
    }
}

function destroy(id) {
    if (!confirm('¿Eliminar esta categoría?')) return
    router.delete(route('admin.categories.destroy', id), { preserveScroll: true })
}

function cancel() {
    showForm.value = false
    editing.value  = null
    form.reset()
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-8">
            <h1 class="font-display text-3xl text-stone-900">Categorías</h1>
            <button @click="showForm = !showForm; editing = null; form.reset()"
                class="btn-primary px-5 py-2.5 text-sm">
                + Nueva categoría
            </button>
        </div>

        <!-- Formulario -->
        <div v-if="showForm" class="bg-white border border-stone-100 rounded p-6 mb-6 space-y-4">
            <h2 class="font-medium text-stone-800">
                {{ editing ? 'Editar categoría' : 'Nueva categoría' }}
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">Nombre *</label>
                    <input v-model="form.name" @input="generateSlug" type="text" class="input-base"/>
                    <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
                </div>
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">Slug *</label>
                    <input v-model="form.slug" type="text" class="input-base font-mono text-sm"/>
                </div>
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">Categoría padre (opcional)</label>
                    <select v-model="form.parent_id" class="input-base">
                        <option :value="null">— Sin padre —</option>
                        <option v-for="cat in categories.filter(c => !c.parent_id)" :key="cat.id" :value="cat.id">
                            {{ cat.name }}
                        </option>
                    </select>
                </div>
                <div class="flex items-center gap-3 pt-5">
                    <input v-model="form.is_active" type="checkbox" id="cat_active" class="w-4 h-4 accent-stone-900"/>
                    <label for="cat_active" class="text-sm text-stone-600">Activa</label>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button @click="submit" :disabled="form.processing" class="btn-primary px-6 py-2.5 text-sm disabled:opacity-50">
                    {{ form.processing ? 'Guardando...' : editing ? 'Actualizar' : 'Crear' }}
                </button>
                <button @click="cancel" class="btn-secondary px-6 py-2.5 text-sm">Cancelar</button>
            </div>
        </div>

        <!-- Lista -->
        <div class="bg-white border border-stone-100 rounded overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-stone-100 bg-stone-50">
                        <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Nombre</th>
                        <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Slug</th>
                        <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Padre</th>
                        <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Estado</th>
                        <th class="px-6 py-3"/>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    <tr v-for="cat in categories" :key="cat.id" class="hover:bg-stone-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-stone-800">{{ cat.name }}</td>
                        <td class="px-6 py-4 text-xs text-stone-400 font-mono">{{ cat.slug }}</td>
                        <td class="px-6 py-4 text-xs text-stone-500">{{ cat.parent?.name ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="badge"
                                :class="cat.is_active ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-400'">
                                {{ cat.is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <button @click="edit(cat)" class="text-xs text-stone-400 hover:text-stone-700 transition-colors">
                                    Editar
                                </button>
                                <button @click="destroy(cat.id)" class="text-xs text-stone-400 hover:text-red-500 transition-colors">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>