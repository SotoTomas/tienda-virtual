<script setup>
import StoreLayout from '@/Layouts/StoreLayout.vue'
import { Link } from '@inertiajs/vue3'

defineOptions({ layout: StoreLayout })

defineProps({
    order: Object,
})

const statusLabel = {
    pending:    { label: 'Pendiente',   class: 'bg-yellow-100 text-yellow-700' },
    confirmed:  { label: 'Confirmado',  class: 'bg-blue-100 text-blue-700' },
    processing: { label: 'Preparando',  class: 'bg-purple-100 text-purple-700' },
    shipped:    { label: 'Enviado',     class: 'bg-indigo-100 text-indigo-700' },
    delivered:  { label: 'Entregado',   class: 'bg-green-100 text-green-700' },
    cancelled:  { label: 'Cancelado',   class: 'bg-red-100 text-red-700' },
    refunded:   { label: 'Reembolsado', class: 'bg-stone-100 text-stone-500' },
}
</script>

<template>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Header -->
        <div class="flex items-center gap-4 mb-8">
            <Link :href="route('orders.index')" class="text-stone-400 hover:text-stone-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
            </Link>
            <div>
                <h1 class="font-display text-3xl text-stone-900">Pedido #{{ order.order_number }}</h1>
                <p class="text-stone-400 text-sm mt-1">{{ order.created_at }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Items -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Estado del envío -->
                <div class="bg-white border border-stone-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-medium text-stone-800">Estado del pedido</h2>
                        <span class="badge"
                            :class="statusLabel[order.status?.value ?? order.status]?.class ?? 'bg-stone-100 text-stone-500'">
                            {{ statusLabel[order.status?.value ?? order.status]?.label ?? order.status }}
                        </span>
                    </div>

                    <!-- Timeline -->
                    <div class="space-y-3">
                        <div v-for="(event, i) in [
                            { label: 'Pedido realizado',  date: order.created_at,  done: true },
                            { label: 'Pago confirmado',   date: order.paid_at,     done: !!order.paid_at },
                            { label: 'En preparación',    date: null,              done: ['processing','shipped','delivered'].includes(order.status?.value ?? order.status) },
                            { label: 'Enviado',           date: order.shipped_at,  done: !!order.shipped_at },
                            { label: 'Entregado',         date: order.delivered_at,done: !!order.delivered_at },
                        ]" :key="i" class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 mt-0.5"
                                :class="event.done ? 'bg-green-500' : 'bg-stone-200'">
                                <svg v-if="event.done" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm" :class="event.done ? 'text-stone-800 font-medium' : 'text-stone-400'">
                                    {{ event.label }}
                                </p>
                                <p v-if="event.date" class="text-xs text-stone-400">{{ event.date }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos -->
                <div class="bg-white border border-stone-100 rounded overflow-hidden">
                    <div class="px-6 py-4 border-b border-stone-100">
                        <h2 class="font-medium text-stone-800">Productos</h2>
                    </div>
                    <div class="divide-y divide-stone-50">
                        <div v-for="item in order.items" :key="item.id"
                            class="px-6 py-4 flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-stone-800">{{ item.product_name }}</p>
                                <p v-if="item.variant_name" class="text-xs text-stone-400">{{ item.variant_name }}</p>
                                <p class="text-xs text-stone-400 mt-0.5">
                                    {{ item.quantity }} × ${{ Number(item.unit_price).toLocaleString('es-AR') }}
                                </p>
                            </div>
                            <span class="text-sm font-semibold shrink-0">
                                ${{ Number(item.subtotal).toLocaleString('es-AR') }}
                            </span>
                        </div>
                    </div>
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
                            <span>{{ order.shipping_cost > 0 ? '$' + Number(order.shipping_cost).toLocaleString('es-AR') : 'Gratis' }}</span>
                        </div>
                        <div class="flex justify-between font-semibold border-t border-stone-100 pt-2">
                            <span>Total</span>
                            <span>${{ Number(order.total).toLocaleString('es-AR') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">

                <!-- Dirección -->
                <div class="bg-white border border-stone-100 rounded p-6">
                    <h2 class="font-medium text-stone-800 mb-4">Dirección de envío</h2>
                    <p class="text-sm font-medium text-stone-700">{{ order.shipping_address?.name }}</p>
                    <p class="text-sm text-stone-500 mt-1">
                        {{ order.shipping_address?.street }} {{ order.shipping_address?.number }}
                        <span v-if="order.shipping_address?.floor">, Piso {{ order.shipping_address.floor }}</span>
                    </p>
                    <p class="text-sm text-stone-500">
                        {{ order.shipping_address?.city }}, {{ order.shipping_address?.state }}
                        {{ order.shipping_address?.postal_code }}
                    </p>
                    <p v-if="order.shipping_address?.phone" class="text-sm text-stone-500 mt-1">
                        Tel: {{ order.shipping_address.phone }}
                    </p>
                </div>

                <!-- Pago -->
                <div class="bg-white border border-stone-100 rounded p-6">
                    <h2 class="font-medium text-stone-800 mb-4">Pago</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-stone-500">Método</span>
                            <span class="capitalize">{{ order.payment_method ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500">Estado</span>
                            <span :class="order.payment_status?.value === 'paid' || order.payment_status === 'paid'
                                ? 'text-green-600 font-medium' : 'text-yellow-600'">
                                {{ order.payment_status?.value === 'paid' || order.payment_status === 'paid'
                                    ? 'Pagado' : 'Pendiente' }}
                            </span>
                        </div>
                        <div v-if="order.paid_at" class="flex justify-between">
                            <span class="text-stone-500">Fecha</span>
                            <span>{{ order.paid_at }}</span>
                        </div>
                    </div>
                </div>

                <!-- Notas -->
                <div v-if="order.notes" class="bg-white border border-stone-100 rounded p-6">
                    <h2 class="font-medium text-stone-800 mb-2">Notas</h2>
                    <p class="text-sm text-stone-600">{{ order.notes }}</p>
                </div>
            </div>
        </div>
    </div>
</template>