<script setup>
import { computed, ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { productImage } from '@/utils/catalog'

const cartStore = useCartStore()
const cart = computed(() => cartStore.cartView)

const couponCode = ref('')
const applying = ref(false)

function updateQuantity(id, quantity) {
    cartStore.updateQuantity(id, quantity)
}

function removeItem(id) {
    cartStore.removeItem(id)
}

async function applyCoupon() {
    applying.value = true
    await cartStore.applyCoupon(couponCode.value)
    applying.value = false
}

function removeCoupon() {
    cartStore.removeCoupon()
}
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="font-display text-4xl text-stone-900 mb-10">Tu carrito</h1>

        <div v-if="cart.items?.length" class="grid grid-cols-1 lg:grid-cols-3 gap-12">

            <div class="lg:col-span-2 space-y-6">
                <div v-for="item in cart.items" :key="item.id"
                    class="flex gap-5 pb-6 border-b border-stone-100">
                    <div class="w-24 h-28 bg-stone-100 shrink-0 overflow-hidden">
                        <img v-if="item.product.main_image"
                            :src="productImage(item.product.main_image)"
                            :alt="item.product.name"
                            class="w-full h-full object-cover"
                        />
                    </div>

                    <div class="flex-1 min-w-0 flex flex-col justify-between">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <RouterLink :to="`/productos/${item.product.slug}`"
                                    class="text-sm font-medium text-stone-800 hover:text-brand-600 transition-colors">
                                    {{ item.product.name }}
                                </RouterLink>
                                <p v-if="item.variant" class="text-xs text-stone-400 mt-0.5">
                                    {{ item.variant.name }}
                                </p>
                            </div>
                            <button @click="removeItem(item.id)"
                                class="text-stone-300 hover:text-red-400 transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="flex items-center justify-between mt-3">
                            <div class="flex items-center border border-stone-200">
                                <button @click="updateQuantity(item.id, item.quantity - 1)"
                                    :disabled="item.quantity <= 1"
                                    class="w-8 h-8 flex items-center justify-center text-stone-500 hover:text-stone-900 disabled:opacity-30">
                                    −
                                </button>
                                <span class="w-8 text-center text-sm">{{ item.quantity }}</span>
                                <button @click="updateQuantity(item.id, item.quantity + 1)"
                                    :disabled="item.quantity >= item.product.stock"
                                    class="w-8 h-8 flex items-center justify-center text-stone-500 hover:text-stone-900 disabled:opacity-30">
                                    +
                                </button>
                            </div>
                            <span class="text-sm font-semibold text-stone-900">
                                ${{ Number(item.subtotal).toLocaleString('es-AR') }}
                            </span>
                        </div>
                    </div>
                </div>

                <RouterLink to="/productos"
                    class="inline-flex items-center gap-2 text-sm text-stone-500 hover:text-stone-900 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                    </svg>
                    Seguir comprando
                </RouterLink>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-stone-50 border border-stone-100 p-6 space-y-6">
                    <h2 class="font-display text-xl text-stone-900">Resumen</h2>

                    <div>
                        <p class="text-xs font-medium tracking-widest uppercase text-stone-500 mb-3">Cupón de descuento</p>
                        <div v-if="!cart.coupon_code" class="flex gap-2">
                            <input
                                v-model="couponCode"
                                type="text"
                                placeholder="CÓDIGO"
                                class="input-base text-sm flex-1 uppercase"
                                @keyup.enter="applyCoupon"
                            />
                            <button @click="applyCoupon" :disabled="applying || !couponCode"
                                class="btn-primary px-4 py-2 text-xs disabled:opacity-50">
                                Aplicar
                            </button>
                        </div>
                        <div v-else class="flex items-center justify-between bg-brand-50 border border-brand-200 px-4 py-3">
                            <span class="text-sm font-medium text-brand-700">{{ cart.coupon_code }}</span>
                            <button @click="removeCoupon" class="text-xs text-brand-500 hover:text-red-500 transition-colors">
                                Quitar
                            </button>
                        </div>
                    </div>

                    <div class="space-y-3 border-t border-stone-200 pt-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-stone-500">Subtotal</span>
                            <span>${{ Number(cart.subtotal).toLocaleString('es-AR') }}</span>
                        </div>
                        <div v-if="cart.coupon_discount > 0" class="flex justify-between text-sm text-green-600">
                            <span>Descuento</span>
                            <span>− ${{ Number(cart.coupon_discount).toLocaleString('es-AR') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-stone-500">Envío</span>
                            <span class="text-stone-400">Calculado al pagar</span>
                        </div>
                        <div class="flex justify-between font-semibold text-base border-t border-stone-200 pt-3">
                            <span>Total</span>
                            <span>${{ Number(cart.total).toLocaleString('es-AR') }}</span>
                        </div>
                    </div>

                    <button
                        disabled
                        class="btn-primary w-full py-4 tracking-widest uppercase text-xs text-center block opacity-50 cursor-not-allowed"
                        title="Checkout disponible solo con backend Laravel"
                    >
                        Finalizar compra
                    </button>

                    <div class="flex items-center justify-center gap-3 pt-2">
                        <span class="text-[10px] text-stone-400 tracking-wide">Pagá con</span>
                        <span class="text-xs text-stone-500 font-medium">MercadoPago</span>
                        <span class="text-xs text-stone-300">·</span>
                        <span class="text-xs text-stone-500 font-medium">Tarjetas</span>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="flex flex-col items-center justify-center py-32 text-stone-400">
            <svg class="w-20 h-20 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
            </svg>
            <p class="font-display text-2xl text-stone-600 mb-2">Tu carrito está vacío</p>
            <p class="text-sm mb-8">Explorá nuestro catálogo y encontrá algo que te guste.</p>
            <RouterLink to="/productos" class="btn-primary px-8 py-3 tracking-widest uppercase text-xs">
                Ver productos
            </RouterLink>
        </div>
    </div>
</template>
