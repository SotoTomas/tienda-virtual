<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    orders:  Object,
    filters: Object,
})

const statusFilter = ref(props.filters?.status ?? '')

const statusLabel = {
    pending:    { label: 'Pendiente',  class: 'bg-yellow-100 text-yellow-700' },
    confirmed:  { label: 'Confirmado', class: 'bg-blue-100 text-blue-700' },
    processing: { label: 'Preparando', class: 'bg-purple-100 text-purple-700' },
    shipped:    { label: 'Enviado',    class: 'bg-indigo-100 text-indigo-700' },
    delivered:  { label: 'Entregado',  class: 'bg-green-100 text-green-700' },
    cancelled:  { label: 'Cancelado',  class: 'bg-red-100 text-red-700' },
    refunded:   { label: 'Reembolsado',class: 'bg-stone-100 text-stone-500' },
}

function filterByStatus(status) {
    statusFilter.value = status
    router.get(route('admin.orders.index'), { status }, { preserveScroll: true, replace: true })
}
</script>

<template>
    <div>
        <div class="mb-8">
            <h1 class="font-display text-3xl text-stone-900">Pedidos</h1>
            <p class="text-stone-500 text-sm mt-1">{{ orders.total }} pedidos en total</p>
        </div>

        <!-- Filtros por estado -->
        <div class="flex flex-wrap gap-2 mb-6">
            <button @click="filterByStatus('')"
                class="px-4 py-2 text-xs border transition-colors"
                :class="!statusFilter ? 'bg-stone-900 text-white border-stone-900' : 'border-stone-200 text-stone-600 hover:border-stone-400'">
                Todos
            </button>
            <button v-for="(s, key) in statusLabel" :key="key"
                @click="filterByStatus(key)"
                class="px-4 py-2 text-xs border transition-colors"
                :class="statusFilter === key ? 'bg-stone-900 text-white border-stone-900' : 'border-stone-200 text-stone-600 hover:border-stone-400'">
                {{ s.label }}
            </button>
        </div>

        <div class="bg-white border border-stone-100 rounded overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-stone-100 bg-stone-50">
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Pedido</th>
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Cliente</th>
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Total</th>
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Pago</th>
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Estado</th>
                            <th class="text-left text-xs text-stone-400 font-medium px-6 py-3">Fecha</th>
                            <th class="px-6 py-3"/>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-50">
                        <tr v-for="order in orders.data" :key="order.id"
                            class="hover:bg-stone-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-stone-800">#{{ order.order_number }}</td>
                            <td class="px-6 py-4 text-sm text-stone-600">{{ order.customer }}</td>
                            <td class="px-6 py-4 text-sm font-semibold">${{ Number(order.total).toLocaleString('es-AR') }}</td>
                            <td class="px-6 py-4">
                                <span class="badge text-xs"
                                    :class="order.payment_status?.value === 'paid' || order.payment_status === 'paid'
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-yellow-100 text-yellow-700'">
                                    {{ order.payment_status?.value === 'paid' || order.payment_status === 'paid' ? 'Pagado' : 'Sin pagar' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge"
                                    :class="statusLabel[order.status?.value ?? order.status]?.class ?? 'bg-stone-100 text-stone-500'">
                                    {{ statusLabel[order.status?.value ?? order.status]?.label ?? order.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-stone-400">{{ order.created_at }}</td>
                            <td class="px-6 py-4">
                                <Link :href="route('admin.orders.show', order.id)"
                                    class="text-xs text-stone-400 hover:text-stone-700 transition-colors">
                                    Ver →
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div v-if="orders.last_page > 1" class="px-6 py-4 border-t border-stone-100 flex justify-center gap-2">
                <Link v-for="link in orders.links" :key="link.label"
                    :href="link.url ?? '#'" v-html="link.label"
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