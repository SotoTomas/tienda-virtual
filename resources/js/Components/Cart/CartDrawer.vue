<script setup>
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
    open: Boolean,
    cart: Object,
})
const emit = defineEmits(['close'])

function removeItem(id) {
    router.delete(route('cart.remove', id), { preserveScroll: true })
}

function updateQuantity(id, quantity) {
    router.patch(route('cart.update', id), { quantity }, { preserveScroll: true })
}
</script>

<template>
    <Teleport to="body">
        <Transition name="overlay">
            <div v-if="open" @click="emit('close')"
                class="fixed inset-0 z-50 bg-stone-900/50 backdrop-blur-sm" />
        </Transition>

        <Transition name="drawer-right">
            <div v-if="open"
                class="fixed inset-y-0 right-0 z-50 w-full sm:w-96 bg-white flex flex-col shadow-2xl">

                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-5 border-b border-stone-100">
                    <h2 class="font-display text-xl text-stone-900">
                        Tu carrito
                        <span v-if="cart.item_count" class="text-stone-400 text-base font-normal ml-1">
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
                <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6">
                    <div v-if="!cart.items?.length" class="flex flex-col items-center justify-center h-full gap-4 text-stone-400 py-16">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                        </svg>
                        <p class="text-sm">Tu carrito está vacío</p>
                        <button @click="emit('close')" class="btn-primary text-xs py-2.5 px-6">
                            Seguir comprando
                        </button>
                    </div>

                    <div v-for="item in cart.items" :key="item.id" class="flex gap-4">
                        <!-- Imagen -->
                        <div class="w-20 h-24 bg-stone-100 shrink-0 overflow-hidden">
                            <img v-if="item.product.main_image"
                                :src="`/storage/${item.product.main_image}`"
                                :alt="item.product.name"
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <Link :href="route('products.show', item.product.slug)"
                                @click="emit('close')"
                                class="text-sm font-medium text-stone-800 hover:text-brand-600 line-clamp-1">
                                {{ item.product.name }}
                            </Link>
                            <p v-if="item.variant" class="text-xs text-stone-400 mt-0.5">
                                {{ item.variant.name }}
                            </p>
                            <p class="text-sm font-semibold text-stone-900 mt-1">
                                ${{ Number(item.unit_price).toLocaleString('es-AR') }}
                            </p>
                            <!-- Cantidad -->
                            <div class="flex items-center gap-3 mt-2">
                                <div class="flex items-center border border-stone-200">
                                    <button @click="updateQuantity(item.id, item.quantity - 1)"
                                        :disabled="item.quantity <= 1"
                                        class="w-7 h-7 flex items-center justify-center text-stone-500 hover:text-stone-900 disabled:opacity-30 text-sm">
                                        −
                                    </button>
                                    <span class="w-7 text-center text-sm">{{ item.quantity }}</span>
                                    <button @click="updateQuantity(item.id, item.quantity + 1)"
                                        :disabled="item.quantity >= item.product.stock"
                                        class="w-7 h-7 flex items-center justify-center text-stone-500 hover:text-stone-900 disabled:opacity-30 text-sm">
                                        +
                                    </button>
                                </div>
                                <button @click="removeItem(item.id)" class="text-xs text-stone-400 hover:text-red-500 transition-colors">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div v-if="cart.items?.length" class="px-6 py-6 border-t border-stone-100 space-y-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500">Subtotal</span>
                        <span class="font-semibold">${{ Number(cart.total ?? 0).toLocaleString('es-AR') }}</span>
                    </div>
                    <p class="text-xs text-stone-400">Envío calculado al finalizar la compra</p>
                    <Link :href="route('checkout.index')" @click="emit('close')" class="btn-primary w-full py-4 tracking-widest uppercase text-xs">
                        Finalizar compra
                    </Link>
                    <Link :href="route('cart.index')" @click="emit('close')" class="btn-secondary w-full py-3 text-xs">
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