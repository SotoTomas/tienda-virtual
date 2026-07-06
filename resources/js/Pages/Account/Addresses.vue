<script setup>
import StoreLayout from '@/Layouts/StoreLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: StoreLayout })

defineProps({
    addresses: Array,
})

const showForm = ref(false)
const editing  = ref(null)

const form = useForm({
    name:        '',
    phone:       '',
    street:      '',
    number:      '',
    floor:       '',
    apartment:   '',
    city:        '',
    state:       '',
    postal_code: '',
    country:     'AR',
    is_default:  false,
})

function edit(address) {
    editing.value    = address.id
    form.name        = address.name
    form.phone       = address.phone ?? ''
    form.street      = address.street
    form.number      = address.number
    form.floor       = address.floor ?? ''
    form.apartment   = address.apartment ?? ''
    form.city        = address.city
    form.state       = address.state
    form.postal_code = address.postal_code
    form.country     = address.country
    form.is_default  = address.is_default
    showForm.value   = true
}

function submit() {
    if (editing.value) {
        form.patch(route('account.addresses.update', editing.value), {
            onSuccess: () => { showForm.value = false; editing.value = null; form.reset() }
        })
    } else {
        form.post(route('account.addresses.store'), {
            onSuccess: () => { showForm.value = false; form.reset() }
        })
    }
}

function destroy(id) {
    if (!confirm('¿Eliminar esta dirección?')) return
    router.delete(route('account.addresses.destroy', id), { preserveScroll: true })
}

function cancel() {
    showForm.value = false
    editing.value  = null
    form.reset()
}
</script>

<template>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex items-center justify-between mb-8">
            <h1 class="font-display text-4xl text-stone-900">Mis direcciones</h1>
            <button @click="showForm = true; editing = null; form.reset()"
                class="btn-primary px-5 py-2.5 text-sm">
                + Nueva dirección
            </button>
        </div>

        <!-- Formulario -->
        <div v-if="showForm" class="bg-stone-50 border border-stone-200 p-6 mb-8 space-y-4">
            <h2 class="font-medium text-stone-800">
                {{ editing ? 'Editar dirección' : 'Nueva dirección' }}
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs text-stone-500 mb-1.5">Nombre completo *</label>
                    <input v-model="form.name" type="text" class="input-base"
                        :class="form.errors.name ? 'border-red-400' : ''"/>
                    <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
                </div>
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">Teléfono</label>
                    <input v-model="form.phone" type="tel" class="input-base"/>
                </div>
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">Calle *</label>
                    <input v-model="form.street" type="text" class="input-base"
                        :class="form.errors.street ? 'border-red-400' : ''"/>
                </div>
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">Número *</label>
                    <input v-model="form.number" type="text" class="input-base"
                        :class="form.errors.number ? 'border-red-400' : ''"/>
                </div>
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">Piso</label>
                    <input v-model="form.floor" type="text" class="input-base"/>
                </div>
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">Departamento</label>
                    <input v-model="form.apartment" type="text" class="input-base"/>
                </div>
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">Ciudad *</label>
                    <input v-model="form.city" type="text" class="input-base"
                        :class="form.errors.city ? 'border-red-400' : ''"/>
                </div>
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">Provincia *</label>
                    <input v-model="form.state" type="text" class="input-base"
                        :class="form.errors.state ? 'border-red-400' : ''"/>
                </div>
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">Código postal *</label>
                    <input v-model="form.postal_code" type="text" class="input-base"
                        :class="form.errors.postal_code ? 'border-red-400' : ''"/>
                </div>
                <div class="flex items-center gap-3 pt-4">
                    <input v-model="form.is_default" type="checkbox" id="is_default" class="w-4 h-4 accent-stone-900"/>
                    <label for="is_default" class="text-sm text-stone-600 cursor-pointer">
                        Usar como dirección principal
                    </label>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button @click="submit" :disabled="form.processing"
                    class="btn-primary px-6 py-2.5 text-sm disabled:opacity-50">
                    {{ form.processing ? 'Guardando...' : editing ? 'Actualizar' : 'Agregar' }}
                </button>
                <button @click="cancel" class="btn-secondary px-6 py-2.5 text-sm">
                    Cancelar
                </button>
            </div>
        </div>

        <!-- Lista de direcciones -->
        <div v-if="addresses?.length" class="space-y-4">
            <div v-for="address in addresses" :key="address.id"
                class="bg-white border p-6 relative"
                :class="address.is_default ? 'border-stone-900' : 'border-stone-100'">

                <div v-if="address.is_default"
                    class="absolute top-4 right-4 badge bg-stone-900 text-white text-[10px] tracking-widest uppercase">
                    Principal
                </div>

                <p class="text-sm font-medium text-stone-800">{{ address.name }}</p>
                <p v-if="address.phone" class="text-xs text-stone-400 mt-0.5">{{ address.phone }}</p>
                <p class="text-sm text-stone-500 mt-2">
                    {{ address.street }} {{ address.number }}
                    <span v-if="address.floor">, Piso {{ address.floor }}</span>
                    <span v-if="address.apartment">, Dto {{ address.apartment }}</span>
                </p>
                <p class="text-sm text-stone-500">
                    {{ address.city }}, {{ address.state }} {{ address.postal_code }}
                </p>

                <div class="flex gap-4 mt-4">
                    <button @click="edit(address)"
                        class="text-xs text-stone-400 hover:text-stone-700 transition-colors">
                        Editar
                    </button>
                    <button @click="destroy(address.id)"
                        class="text-xs text-stone-400 hover:text-red-500 transition-colors">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>

        <div v-else class="py-20 text-center text-stone-400">
            <p class="text-sm">No tenés direcciones guardadas todavía.</p>
        </div>
    </div>
</template>
