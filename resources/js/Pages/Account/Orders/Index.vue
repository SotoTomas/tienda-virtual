<script setup>
import StoreLayout from '@/Layouts/StoreLayout.vue'
import { Link } from '@inertiajs/vue3'

defineOptions({ layout: StoreLayout })

defineProps({
    orders: Object,
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
        <h1 class="font-display text-4xl text-stone-900 mb-10">Mis pedidos</h1>

        <div v-if="orders.data?.length" class="space-y-4">
            <div v-for="order in orders.data" :key="order.id"
                class="border border-stone-100 p-6 hover:border-stone-300 transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="font-medium text-stone-800">#{{ order.order_number }}</span>
                            <span class="badge"
                                :class="statusLabel[order.status]?.class ?? 'bg-stone-100 text-stone-500'">
                                {{ statusLabel[order.status]?.label ?? order.status }}
                            </span>
                        </div>
                        <p class="text-xs text-stone-400 mt-1">{{ order.created_at }}</p>
                    </div>
                    <span class="font-semibold text-stone-900 shrink-0">
                        ${{ Number(order.total).toLocaleString('es-AR') }}
                    </span>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <p class="text-sm text-stone-500">
                        {{ order.items_count }} {{ order.items_count === 1 ? 'producto' : 'productos' }}
                    </p>
                    <Link :href="route('orders.show', order.id)"
                        class="text-xs text-stone-500 hover:text-stone-900 transition-colors flex items-center gap-1">
                        Ver detalle
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                        </svg>
                    </Link>
                </div>
            </div>
        </div>

        <div v-else class="flex flex-col items-center justify-center py-24 text-stone-400">
            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
            </svg>
            <p class="text-sm mb-6">Todavía no tenés pedidos.</p>
            <Link :href="route('products.index')" class="btn-primary px-8 py-3 text-xs tracking-widest uppercase">
                Empezar a comprar
            </Link>
        </div>
    </div>
</template>
