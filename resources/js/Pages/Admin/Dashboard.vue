<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'

defineOptions({ layout: AdminLayout })

defineProps({
    stats:        Object,
    recentOrders: Array,
})

const statusLabel = {
    pending:    { label: 'Pendiente',  class: 'bg-yellow-100 text-yellow-700' },
    confirmed:  { label: 'Confirmado', class: 'bg-blue-100 text-blue-700' },
    processing: { label: 'Preparando', class: 'bg-purple-100 text-purple-700' },
    shipped:    { label: 'Enviado',    class: 'bg-indigo-100 text-indigo-700' },
    delivered:  { label: 'Entregado',  class: 'bg-green-100 text-green-700' },
    cancelled:  { label: 'Cancelado',  class: 'bg-red-100 text-red-700' },
}
</script>

<template>
    <div>
        <div class="mb-8">
            <h1 class="font-display text-3xl text-stone-900">Dashboard</h1>
            <p class="text-stone-500 text-sm mt-1">Resumen general de la tienda</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-10">
            <div v-for="stat in [
                { label: 'Pedidos totales',   value: stats.total_orders,   icon: '📦' },
                { label: 'Pendientes',         value: stats.pending_orders, icon: '⏳', alert: stats.pending_orders > 0 },
                { label: 'Ingresos',           value: '$' + Number(stats.total_revenue).toLocaleString('es-AR'), icon: '💰' },
                { label: 'Productos activos',  value: stats.total_products, icon: '🛍️' },
                { label: 'Stock bajo',         value: stats.low_stock,      icon: '⚠️', alert: stats.low_stock > 0 },
                { label: 'Usuarios',           value: stats.total_users,    icon: '👥' },
            ]" :key="stat.label"
                class="bg-white border p-5 rounded"
                :class="stat.alert ? 'border-orange-200' : 'border-stone-100'">
                <p class="text-2xl mb-2">{{ stat.icon }}</p>
                <p class="text-2xl font-semibold text-stone-900">{{ stat.value }}</p>
                <p class="text-xs text-stone-400 mt-1">{{ stat.label }}</p>
            </div>
        </div>

        <!-- Pedidos recientes -->
        <div class="bg-white border border-stone-100 rounded">
            <div class="px-6 py-4 border-b border-stone-100 flex items-center justify-between">
                <h2 class="font-medium text-stone-800">Pedidos recientes</h2>
                <Link :href="route('admin.orders.index')" class="text-xs text-stone-400 hover:text-stone-700 transition-colors">
                    Ver todos →
                </Link>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-stone-100">
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Pedido</th>
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Cliente</th>
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Total</th>
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Estado</th>
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Fecha</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-50">
                        <tr v-for="order in recentOrders" :key="order.id"
                            class="hover:bg-stone-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-stone-800">#{{ order.order_number }}</td>
                            <td class="px-6 py-4 text-sm text-stone-600">{{ order.customer }}</td>
                            <td class="px-6 py-4 text-sm font-medium">${{ Number(order.total).toLocaleString('es-AR') }}</td>
                            <td class="px-6 py-4">
                                <span class="badge"
                                    :class="statusLabel[order.status?.value ?? order.status]?.class ?? 'bg-stone-100 text-stone-500'">
                                    {{ statusLabel[order.status?.value ?? order.status]?.label ?? order.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-stone-400">{{ order.created_at }}</td>
                            <td class="px-6 py-4">
                                <Link :href="route('admin.orders.show', order.id)"
                                    class="text-xs text-stone-400 hover:text-stone-700">
                                    Ver →
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
