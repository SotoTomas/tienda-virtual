<script setup>
import { computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

const props = defineProps({
    open: Boolean,
})
const emit = defineEmits(['close'])

// Lee el carrito directo de los props globales de Inertia
const page = usePage()
const cart = computed(() => page.props.cart ?? { count: 0, item_count: 0, items: [], total: 0 })

function removeItem(id) {
    router.delete(route('cart.remove', id), { preserveScroll: true })
}

function updateQuantity(id, quantity) {
    if (quantity < 1) return
    router.patch(route('cart.update', id), { quantity }, { preserveScroll: true })
}
</script>

<template>
    <Teleport to="body">
        <!-- Overlay -->
        <Transition name="overlay">
            <div v-if="open" @click="emit('close')"
                class="fixed inset-0 z-50 bg-stone-900/50 backdrop-blur-sm" />
        </Transition>

        <!-- Drawer -->
        <Transition name="drawer-right">
            <div v-if="open"
                class="fixed inset-y-0 right-0 z-50 w-full sm:w-96 bg-white flex flex-col shadow-2xl">

                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-5 border-b border-stone-100">
                    <h2 class="font-display text-xl text-stone-900">
                        Tu carrito
                        <span v-if="cart.item_count > 0" class="text-stone-400 text-base font-normal ml-1">
                            ({{ cart.item_count }})
                        </span>
                    </h2>
                    <button @click="emit('close')" class="btn-ghost p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Items -->
                <div class="flex-1 overflow-y-auto px-6 py-4">

                    <!-- Carrito vacío -->
                    <div v-if="!cart.items?.length"
                        class="flex flex-col items-center justify-center h-full gap-4 text-stone-400 py-16">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                        </svg>
                        <p class="text-sm font-medium text-stone-600">Tu carrito está vacío</p>
                        <p class="text-xs text-stone-400">Agregá productos para verlos acá</p>
                        <button @click="emit('close')" class="btn-primary text-xs py-2.5 px-6 mt-2">
                            Seguir comprando
                        </button>
                    </div>

                    <!-- Lista de items -->
                    <div v-else class="space-y-5 py-2">
                        <div v-for="item in cart.items" :key="item.id"
                            class="flex gap-4 pb-5 border-b border-stone-50 last:border-0">

                            <!-- Imagen -->
                            <div class="w-20 h-24 bg-stone-100 shrink-0 overflow-hidden">
                                <img
                                    v-if="item.product?.main_image"
                                    :src="`/${item.product.main_image}`"
                                    :alt="item.product.name"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center text-stone-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0 flex flex-col justify-between">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <Link
                                            :href="route('products.show', item.product.slug)"
                                            @click="emit('close')"
                                            class="text-sm font-medium text-stone-800 hover:text-brand-600 transition-colors line-clamp-2 leading-snug"
                                        >
                                            {{ item.product.name }}
                                        </Link>
                                        <p v-if="item.variant" class="text-xs text-stone-400 mt-0.5">
                                            {{ item.variant.name }}
                                        </p>
                                    </div>
                                    <!-- Eliminar -->
                                    <button @click="removeItem(item.id)"
                                        class="text-stone-300 hover:text-red-400 transition-colors shrink-0 mt-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                <div class="flex items-center justify-between mt-3">
                                    <!-- Cantidad -->
                                    <div class="flex items-center border border-stone-200">
                                        <button
                                            @click="updateQuantity(item.id, item.quantity - 1)"
                                            :disabled="item.quantity <= 1"
                                            class="w-7 h-7 flex items-center justify-center text-stone-500 hover:text-stone-900 disabled:opacity-30 text-sm transition-colors"
                                        >−</button>
                                        <span class="w-7 text-center text-sm select-none">{{ item.quantity }}</span>
                                        <button
                                            @click="updateQuantity(item.id, item.quantity + 1)"
                                            :disabled="item.quantity >= (item.product?.stock ?? 99)"
                                            class="w-7 h-7 flex items-center justify-center text-stone-500 hover:text-stone-900 disabled:opacity-30 text-sm transition-colors"
                                        >+</button>
                                    </div>
                                    <!-- Subtotal del item -->
                                    <span class="text-sm font-semibold text-stone-900">
                                        ${{ Number(item.subtotal ?? item.unit_price * item.quantity).toLocaleString('es-AR') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer con totales -->
                <div v-if="cart.items?.length" class="px-6 py-5 border-t border-stone-100 space-y-3 bg-stone-50">

                    <!-- Desglose -->
                    <div class="space-y-2">
                        <div v-for="item in cart.items" :key="'total-' + item.id"
                            class="flex justify-between text-xs text-stone-400">
                            <span class="truncate max-w-[200px]">
                                {{ item.product.name }}
                                <span v-if="item.variant"> — {{ item.variant.name }}</span>
                                × {{ item.quantity }}
                            </span>
                            <span class="shrink-0 ml-2">
                                ${{ Number(item.subtotal ?? item.unit_price * item.quantity).toLocaleString('es-AR') }}
                            </span>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="flex justify-between items-center pt-3 border-t border-stone-200">
                        <span class="text-sm font-medium text-stone-700">Total</span>
                        <span class="text-lg font-semibold text-stone-900">
                            ${{ Number(cart.total ?? 0).toLocaleString('es-AR') }}
                        </span>
                    </div>

                    <p class="text-[11px] text-stone-400">Envío calculado al finalizar la compra</p>

                    <!-- CTAs -->
                    <Link
                        :href="route('checkout.index')"
                        @click="emit('close')"
                        class="btn-primary w-full py-3.5 tracking-widest uppercase text-xs text-center block"
                    >
                        Finalizar compra
                    </Link>
                    <Link
                        :href="route('cart.index')"
                        @click="emit('close')"
                        class="btn-secondary w-full py-2.5 text-xs text-center block"
                    >
                        Ver carrito completo
                    </Link>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.overlay-enter-active, .overlay-leave-active { transition: opacity 0.3s ease; }
.overlay-enter-from, .overlay-leave-to { opacity: 0; }
.drawer-right-enter-active, .drawer-right-leave-active { transition: transform 0.3s ease; }
.drawer-right-enter-from, .drawer-right-leave-to { transform: translateX(100%); }
</style>
