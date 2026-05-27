<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: AdminLayout })

defineProps({
    coupons: Array,
})

const showForm = ref(false)

const form = useForm({
    code:           '',
    type:           'percentage',
    value:          '',
    minimum_amount: '',
    usage_limit:    '',
    is_active:      true,
    expires_at:     '',
})

function submit() {
    form.post(route('admin.coupons.store'), {
        onSuccess: () => { showForm.value = false; form.reset() }
    })
}

function destroy(id) {
    if (!confirm('¿Eliminar este cupón?')) return
    router.delete(route('admin.coupons.destroy', id), { preserveScroll: true })
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-8">
            <h1 class="font-display text-3xl text-stone-900">Cupones</h1>
            <button @click="showForm = !showForm" class="btn-primary px-5 py-2.5 text-sm">
                + Nuevo cupón
            </button>
        </div>

        <!-- Formulario -->
        <div v-if="showForm" class="bg-white border border-stone-100 rounded p-6 mb-6 space-y-4">
            <h2 class="font-medium text-stone-800">Nuevo cupón</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">Código *</label>
                    <input v-model="form.code" type="text" class="input-base uppercase font-mono"
                        placeholder="EJ: DESCUENTO20"/>
                    <p v-if="form.errors.code" class="text-xs text-red-500 mt-1">{{ form.errors.code }}</p>
                </div>
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">Tipo *</label>
                    <select v-model="form.type" class="input-base">
                        <option value="percentage">Porcentaje (%)</option>
                        <option value="fixed">Monto fijo ($)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">
                        Valor * {{ form.type === 'percentage' ? '(%)' : '($)' }}
                    </label>
                    <input v-model="form.value" type="number" step="0.01" class="input-base"/>
                    <p v-if="form.errors.value" class="text-xs text-red-500 mt-1">{{ form.errors.value }}</p>
                </div>
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">Monto mínimo</label>
                    <input v-model="form.minimum_amount" type="number" step="0.01" class="input-base" placeholder="Sin mínimo"/>
                </div>
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">Límite de usos</label>
                    <input v-model="form.usage_limit" type="number" class="input-base" placeholder="Sin límite"/>
                </div>
                <div>
                    <label class="block text-xs text-stone-500 mb-1.5">Vencimiento</label>
                    <input v-model="form.expires_at" type="datetime-local" class="input-base text-sm"/>
                </div>
                <div class="flex items-center gap-3 pt-4">
                    <input v-model="form.is_active" type="checkbox" id="coupon_active" class="w-4 h-4 accent-stone-900"/>
                    <label for="coupon_active" class="text-sm text-stone-600">Activo</label>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button @click="submit" :disabled="form.processing" class="btn-primary px-6 py-2.5 text-sm disabled:opacity-50">
                    {{ form.processing ? 'Guardando...' : 'Crear cupón' }}
                </button>
                <button @click="showForm = false; form.reset()" class="btn-secondary px-6 py-2.5 text-sm">
                    Cancelar
                </button>
            </div>
        </div>

        <!-- Lista -->
        <div class="bg-white border border-stone-100 rounded overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-stone-100 bg-stone-50">
                        <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Código</th>
                        <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Descuento</th>
                        <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Usos</th>
                        <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Vencimiento</th>
                        <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Estado</th>
                        <th class="px-6 py-3"/>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    <tr v-for="coupon in coupons" :key="coupon.id" class="hover:bg-stone-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-mono font-medium text-stone-800">{{ coupon.code }}</td>
                        <td class="px-6 py-4 text-sm text-stone-700">
                            {{ coupon.type === 'percentage' ? coupon.value + '%' : '$' + Number(coupon.value).toLocaleString('es-AR') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-500">
                            {{ coupon.used_count }}
                            <span v-if="coupon.usage_limit"> / {{ coupon.usage_limit }}</span>
                        </td>
                        <td class="px-6 py-4 text-xs text-stone-400">
                            {{ coupon.expires_at ?? 'Sin vencimiento' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="badge"
                                :class="coupon.is_active ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-400'">
                                {{ coupon.is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button @click="destroy(coupon.id)" class="text-xs text-stone-400 hover:text-red-500 transition-colors">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>