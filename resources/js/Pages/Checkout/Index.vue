<script setup>
import StoreLayout from '@/Layouts/StoreLayout.vue'
import { router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

defineOptions({ layout: StoreLayout })

const props = defineProps({
    cart:      Object,
    addresses: Array,
})

const page = usePage()
const step = ref(1) // 1: dirección, 2: pago, 3: confirmación

const form = ref({
    // Dirección
    address_id:   null,
    name:         page.props.auth?.user?.name ?? '',
    phone:        '',
    street:       '',
    number:       '',
    floor:        '',
    apartment:    '',
    city:         '',
    state:        '',
    postal_code:  '',
    country:      'AR',
    save_address: false,
    // Pago
    payment_method: 'mercadopago',
    // Nota
    notes: '',
})

const errors  = ref({})
const loading = ref(false)

function selectAddress(address) {
    form.value.address_id   = address.id
    form.value.name         = address.name
    form.value.phone        = address.phone ?? ''
    form.value.street       = address.street
    form.value.number       = address.number
    form.value.floor        = address.floor ?? ''
    form.value.apartment    = address.apartment ?? ''
    form.value.city         = address.city
    form.value.state        = address.state
    form.value.postal_code  = address.postal_code
}

function nextStep() {
    if (step.value < 3) step.value++
}

function prevStep() {
    if (step.value > 1) step.value--
}

function placeOrder() {
    loading.value = true
    router.post(route('checkout.process'), form.value, {
        onError: (e) => { errors.value = e; loading.value = false },
        onFinish: () => loading.value = false,
    })
}

const orderTotal = computed(() => props.cart?.total ?? 0)
</script>

<template>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="font-display text-4xl text-stone-900 mb-10">Finalizar compra</h1>

        <!-- Steps indicator -->
        <div class="flex items-center gap-3 mb-10">
            <div v-for="(label, i) in ['Dirección', 'Pago', 'Confirmar']" :key="i"
                class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold transition-colors"
                    :class="step > i + 1
                        ? 'bg-green-500 text-white'
                        : step === i + 1
                            ? 'bg-stone-900 text-white'
                            : 'bg-stone-200 text-stone-400'">
                    <svg v-if="step > i + 1" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span v-else>{{ i + 1 }}</span>
                </div>
                <span class="text-sm hidden sm:block"
                    :class="step === i + 1 ? 'text-stone-900 font-medium' : 'text-stone-400'">
                    {{ label }}
                </span>
                <svg v-if="i < 2" class="w-4 h-4 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                </svg>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

            <!-- ── Formulario ──────────────────────────────── -->
            <div class="lg:col-span-2">

                <!-- Step 1: Dirección -->
                <div v-if="step === 1" class="space-y-6">
                    <h2 class="font-display text-2xl text-stone-800">Dirección de envío</h2>

                    <!-- Direcciones guardadas -->
                    <div v-if="addresses?.length" class="space-y-3">
                        <p class="text-xs text-stone-500 tracking-widest uppercase">Tus direcciones guardadas</p>
                        <div v-for="addr in addresses" :key="addr.id"
                            @click="selectAddress(addr)"
                            class="border p-4 cursor-pointer transition-colors"
                            :class="form.address_id === addr.id
                                ? 'border-stone-900 bg-stone-50'
                                : 'border-stone-200 hover:border-stone-400'">
                            <p class="text-sm font-medium text-stone-800">{{ addr.name }}</p>
                            <p class="text-xs text-stone-500 mt-1">
                                {{ addr.street }} {{ addr.number }}, {{ addr.city }}, {{ addr.state }}
                            </p>
                        </div>
                        <p class="text-xs text-stone-400 pt-1">O ingresá una nueva dirección:</p>
                    </div>

                    <!-- Formulario de dirección -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs text-stone-500 mb-1.5">Nombre completo</label>
                            <input v-model="form.name" type="text" class="input-base"
                                :class="errors.name ? 'border-red-400' : ''"/>
                            <p v-if="errors.name" class="text-xs text-red-500 mt-1">{{ errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-stone-500 mb-1.5">Teléfono</label>
                            <input v-model="form.phone" type="tel" class="input-base"/>
                        </div>
                        <div>
                            <label class="block text-xs text-stone-500 mb-1.5">Calle</label>
                            <input v-model="form.street" type="text" class="input-base"
                                :class="errors.street ? 'border-red-400' : ''"/>
                            <p v-if="errors.street" class="text-xs text-red-500 mt-1">{{ errors.street }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-stone-500 mb-1.5">Número</label>
                            <input v-model="form.number" type="text" class="input-base"
                                :class="errors.number ? 'border-red-400' : ''"/>
                        </div>
                        <div>
                            <label class="block text-xs text-stone-500 mb-1.5">Piso (opcional)</label>
                            <input v-model="form.floor" type="text" class="input-base"/>
                        </div>
                        <div>
                            <label class="block text-xs text-stone-500 mb-1.5">Departamento (opcional)</label>
                            <input v-model="form.apartment" type="text" class="input-base"/>
                        </div>
                        <div>
                            <label class="block text-xs text-stone-500 mb-1.5">Ciudad</label>
                            <input v-model="form.city" type="text" class="input-base"
                                :class="errors.city ? 'border-red-400' : ''"/>
                        </div>
                        <div>
                            <label class="block text-xs text-stone-500 mb-1.5">Provincia</label>
                            <input v-model="form.state" type="text" class="input-base"
                                :class="errors.state ? 'border-red-400' : ''"/>
                        </div>
                        <div>
                            <label class="block text-xs text-stone-500 mb-1.5">Código postal</label>
                            <input v-model="form.postal_code" type="text" class="input-base"
                                :class="errors.postal_code ? 'border-red-400' : ''"/>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs text-stone-500 mb-1.5">Notas para el envío (opcional)</label>
                            <textarea v-model="form.notes" rows="2" class="input-base resize-none"
                                placeholder="Entre calles, referencias, instrucciones..."/>
                        </div>
                        <div class="sm:col-span-2 flex items-center gap-3">
                            <input v-model="form.save_address" type="checkbox" id="save_address"
                                class="w-4 h-4 accent-stone-900"/>
                            <label for="save_address" class="text-sm text-stone-600 cursor-pointer">
                                Guardar esta dirección para futuras compras
                            </label>
                        </div>
                    </div>

                    <button @click="nextStep" class="btn-primary w-full py-4 tracking-widest uppercase text-sm">
                        Continuar al pago
                    </button>
                </div>

                <!-- Step 2: Método de pago -->
                <div v-if="step === 2" class="space-y-6">
                    <h2 class="font-display text-2xl text-stone-800">Método de pago</h2>

                    <div class="space-y-3">
                        <div v-for="method in [
                            { id: 'mercadopago', label: 'MercadoPago', desc: 'Tarjetas, débito, efectivo' },
                            { id: 'transferencia', label: 'Transferencia bancaria', desc: 'CBU / CVU' },
                        ]" :key="method.id"
                            @click="form.payment_method = method.id"
                            class="border p-5 cursor-pointer transition-colors flex items-center gap-4"
                            :class="form.payment_method === method.id
                                ? 'border-stone-900 bg-stone-50'
                                : 'border-stone-200 hover:border-stone-400'">
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0"
                                :class="form.payment_method === method.id
                                    ? 'border-stone-900'
                                    : 'border-stone-300'">
                                <div v-if="form.payment_method === method.id"
                                    class="w-2.5 h-2.5 rounded-full bg-stone-900"/>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-stone-800">{{ method.label }}</p>
                                <p class="text-xs text-stone-400 mt-0.5">{{ method.desc }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button @click="prevStep" class="btn-secondary flex-1 py-4 text-sm">
                            Volver
                        </button>
                        <button @click="nextStep" class="btn-primary flex-1 py-4 tracking-widest uppercase text-sm">
                            Revisar pedido
                        </button>
                    </div>
                </div>

                <!-- Step 3: Confirmación -->
                <div v-if="step === 3" class="space-y-6">
                    <h2 class="font-display text-2xl text-stone-800">Revisá tu pedido</h2>

                    <!-- Dirección -->
                    <div class="border border-stone-100 p-5">
                        <p class="text-xs text-stone-400 tracking-widest uppercase mb-3">Envío a</p>
                        <p class="text-sm font-medium text-stone-800">{{ form.name }}</p>
                        <p class="text-sm text-stone-500 mt-1">
                            {{ form.street }} {{ form.number }}
                            <span v-if="form.floor">, Piso {{ form.floor }}</span>
                            <span v-if="form.apartment">, Dto {{ form.apartment }}</span>
                        </p>
                        <p class="text-sm text-stone-500">
                            {{ form.city }}, {{ form.state }} {{ form.postal_code }}
                        </p>
                    </div>

                    <!-- Método de pago -->
                    <div class="border border-stone-100 p-5">
                        <p class="text-xs text-stone-400 tracking-widest uppercase mb-3">Pago</p>
                        <p class="text-sm font-medium text-stone-800">
                            {{ form.payment_method === 'mercadopago' ? 'MercadoPago' : 'Transferencia bancaria' }}
                        </p>
                    </div>

                    <!-- Productos -->
                    <div class="border border-stone-100 p-5">
                        <p class="text-xs text-stone-400 tracking-widest uppercase mb-4">Productos</p>
                        <div class="space-y-3">
                            <div v-for="item in cart.items" :key="item.id"
                                class="flex items-center justify-between text-sm">
                                <span class="text-stone-700">
                                    {{ item.product.name }}
                                    <span v-if="item.variant" class="text-stone-400"> — {{ item.variant.name }}</span>
                                    <span class="text-stone-400"> × {{ item.quantity }}</span>
                                </span>
                                <span class="font-medium">${{ Number(item.subtotal).toLocaleString('es-AR') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button @click="prevStep" class="btn-secondary flex-1 py-4 text-sm">
                            Volver
                        </button>
                        <button @click="placeOrder" :disabled="loading"
                            class="btn-primary flex-1 py-4 tracking-widest uppercase text-sm disabled:opacity-50">
                            {{ loading ? 'Procesando...' : 'Confirmar pedido' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- ── Resumen lateral ─────────────────────────── -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 bg-stone-50 border border-stone-100 p-6 space-y-4">
                    <h3 class="font-display text-xl text-stone-900">Tu pedido</h3>

                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <div v-for="item in cart.items" :key="item.id" class="flex gap-3">
                            <div class="w-12 h-14 bg-stone-200 shrink-0 overflow-hidden">
                                <img v-if="item.product.main_image"
                                    :src="`/${item.product.main_image}`"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-stone-800 line-clamp-1">{{ item.product.name }}</p>
                                <p v-if="item.variant" class="text-xs text-stone-400">{{ item.variant.name }}</p>
                                <p class="text-xs text-stone-600 mt-1">
                                    {{ item.quantity }} × ${{ Number(item.unit_price).toLocaleString('es-AR') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-stone-200 pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-stone-500">Subtotal</span>
                            <span>${{ Number(cart.subtotal).toLocaleString('es-AR') }}</span>
                        </div>
                        <div v-if="cart.coupon_discount > 0" class="flex justify-between text-sm text-green-600">
                            <span>Descuento</span>
                            <span>− ${{ Number(cart.coupon_discount).toLocaleString('es-AR') }}</span>
                        </div>
                        <div class="flex justify-between font-semibold border-t border-stone-200 pt-2 mt-2">
                            <span>Total</span>
                            <span>${{ Number(orderTotal).toLocaleString('es-AR') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
