<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    order: Object,
})

const newStatus = ref(props.order.status?.value ?? props.order.status)
const updating  = ref(false)

const statuses = [
    { value: 'pending',    label: 'Pendiente' },
    { value: 'confirmed',  label: 'Confirmado' },
    { value: 'processing', label: 'Preparando' },
    { value: 'shipped',    label: 'Enviado' },
    { value: 'delivered',  label: 'Entregado' },
    { value: 'cancelled',  label: 'Cancelado' },
    { value: 'refunded',   label: 'Reembolsado' },
]

function updateStatus() {
    updating.value = true
    router.patch(route('admin.orders.status', props.order.id), { status: newStatus.value }, {
        preserveScroll: true,
        onFinish: () => updating.value = false,
    })
}
</script>

<template>
    <div>
        <div class="flex items-center gap-4 mb-8">
            <Link :href="route('admin.orders.index')" class="text-stone-400 hover:text-stone-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
            </Link>
            <h1 class="font-display text-3xl text-stone-900">Pedido #{{ order.order_number }}</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Items del pedido -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-stone-100 rounded overflow-hidden">
                    <div class="px-6 py-4 border-b border-stone-100">
                        <h2 class="font-medium text-stone-800">Productos</h2>
                    </div>
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-stone-50 bg-stone-50">
                                <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Producto</th>
                                <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Precio</th>
                                <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Cantidad</th>
                                <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-50">
                            <tr v-for="item in order.items" :key="item.id">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-stone-800">{{ item.product_name }}</p>
                                    <p v-if="item.variant_name" class="text-xs text-stone-400">{{ item.variant_name }}</p>
                                    <p v-if="item.sku" class="text-xs text-stone-400 font-mono">{{ item.sku }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm">${{ Number(item.unit_price).toLocaleString('es-AR') }}</td>
                                <td class="px-6 py-4 text-sm">{{ item.quantity }}</td>
                                <td class="px-6 py-4 text-sm font-semibold">${{ Number(item.subtotal).toLocaleString('es-AR') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Totales -->
                    <div class="px-6 py-4 border-t border-stone-100 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-stone-500">Subtotal</span>
                            <span>${{ Number(order.subtotal).toLocaleString('es-AR') }}</span>
                        </div>
                        <div v-if="order.discount > 0" class="flex justify-between text-sm text-green-600">
                            <span>Descuento</span>
                            <span>− ${{ Number(order.discount).toLocaleString('es-AR') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-stone-500">Envío</span>
                            <span>${{ Number(order.shipping_cost).toLocaleString('es-AR') }}</span>
                        </div>
                        <div class="flex justify-between font-semibold text-base border-t border-stone-100 pt-2">
                            <span>Total</span>
                            <span>${{ Number(order.total).toLocaleString('es-AR') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Dirección de envío -->
                <div class="bg-white border border-stone-100 rounded p-6">
                    <h2 class="font-medium text-stone-800 mb-4">Dirección de envío</h2>
                    <p class="text-sm font-medium text-stone-700">{{ order.shipping_address?.name }}</p>
                    <p class="text-sm text-stone-500 mt-1">
                        {{ order.shipping_address?.street }} {{ order.shipping_address?.number }}
                        <span v-if="order.shipping_address?.floor">, Piso {{ order.shipping_address?.floor }}</span>
                    </p>
                    <p class="text-sm text-stone-500">
                        {{ order.shipping_address?.city }}, {{ order.shipping_address?.state }}
                        {{ order.shipping_address?.postal_code }}
                    </p>
                    <p v-if="order.shipping_address?.phone" class="text-sm text-stone-500 mt-1">
                        Tel: {{ order.shipping_address?.phone }}
                    </p>
                </div>

                <!-- Notas -->
                <div v-if="order.notes" class="bg-white border border-stone-100 rounded p-6">
                    <h2 class="font-medium text-stone-800 mb-2">Notas del cliente</h2>
                    <p class="text-sm text-stone-600">{{ order.notes }}</p>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">

                <!-- Cambiar estado -->
                <div class="bg-white border border-stone-100 rounded p-6 space-y-4">
                    <h2 class="font-medium text-stone-800">Estado del pedido</h2>
                    <select v-model="newStatus" class="input-base text-sm">
                        <option v-for="s in statuses" :key="s.value" :value="s.value">
                            {{ s.label }}
                        </option>
                    </select>
                    <button @click="updateStatus" :disabled="updating"
                        class="btn-primary w-full py-2.5 text-sm disabled:opacity-50">
                        {{ updating ? 'Actualizando...' : 'Actualizar estado' }}
                    </button>
                </div>

                <!-- Info del cliente -->
                <div class="bg-white border border-stone-100 rounded p-6 space-y-3">
                    <h2 class="font-medium text-stone-800">Cliente</h2>
                    <div class="text-sm space-y-1">
                        <p class="font-medium text-stone-700">{{ order.customer }}</p>
                        <p class="text-stone-400">{{ order.customer_email }}</p>
                    </div>
                </div>

                <!-- Info del pago -->
                <div class="bg-white border border-stone-100 rounded p-6 space-y-3">
                    <h2 class="font-medium text-stone-800">Pago</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-stone-500">Método</span>
                            <span class="text-stone-700 capitalize">{{ order.payment_method ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500">Estado</span>
                            <span class="font-medium"
                                :class="order.payment_status?.value === 'paid' || order.payment_status === 'paid'
                                    ? 'text-green-600' : 'text-yellow-600'">
                                {{ order.payment_status?.value === 'paid' || order.payment_status === 'paid' ? 'Pagado' : 'Sin pagar' }}
                            </span>
                        </div>
                        <div v-if="order.paid_at" class="flex justify-between">
                            <span class="text-stone-500">Fecha pago</span>
                            <span class="text-stone-700">{{ order.paid_at }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
