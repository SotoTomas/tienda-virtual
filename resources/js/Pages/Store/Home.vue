<script setup>
import StoreLayout from '@/Layouts/StoreLayout.vue'
import ProductCard from '@/Components/Product/ProductCard.vue'
import { Link } from '@inertiajs/vue3'

defineOptions({ layout: StoreLayout })

defineProps({
    featuredProducts: Array,
    newArrivals:      Array,
    topCategories:    Array,
})
</script>

<template>
    <div>
        <!-- ── Hero ───────────────────────────────────────── -->
        <section class="relative bg-stone-900 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-stone-900 via-stone-900/90 to-transparent z-10" />
            <div class="absolute inset-0 bg-[url('/images/hero-bg.jpg')] bg-cover bg-center opacity-40" />

            <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 lg:py-48">
                <div class="max-w-xl animate-fade-in">
                    <p class="text-brand-400 text-xs tracking-[0.3em] uppercase mb-4">Nueva colección</p>
                    <h1 class="font-display text-5xl lg:text-7xl text-white leading-tight">
                        Estilo que <em>define</em>
                    </h1>
                    <p class="mt-6 text-stone-300 text-lg leading-relaxed">
                        Descubrí las últimas tendencias en moda y accesorios. Calidad premium, precios accesibles.
                    </p>
                    <div class="mt-10 flex flex-wrap gap-4">
                        <Link :href="route('products.index')" class="btn-primary px-8 py-4 text-sm tracking-widest uppercase">
                            Ver colección
                        </Link>
                        <Link :href="route('products.index', { destacados: true })" class="btn-secondary border-stone-600 text-stone-300 hover:border-white hover:text-white px-8 py-4 text-sm tracking-widest uppercase">
                            Lo más vendido
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Categorías ─────────────────────────────────── -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <p class="text-brand-500 text-xs tracking-[0.3em] uppercase mb-2">Explorar</p>
                    <h2 class="font-display text-3xl lg:text-4xl text-stone-900">Por categoría</h2>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <Link
                    v-for="(cat, i) in topCategories"
                    :key="cat.id"
                    :href="route('categories.show', cat.slug)"
                    class="group relative overflow-hidden bg-stone-100 flex items-end p-6"
                    :class="i === 0 ? 'md:col-span-2 aspect-[2/1]' : 'aspect-square'"
                >
                    <div class="absolute inset-0 bg-stone-900/20 group-hover:bg-stone-900/40 transition-colors duration-300" />
                    <div class="relative z-10">
                        <h3 class="font-display text-xl lg:text-2xl text-white">{{ cat.name }}</h3>
                        <p class="text-stone-300 text-xs mt-1">{{ cat.products_count }} productos</p>
                    </div>
                </Link>
            </div>
        </section>

        <!-- ── Productos destacados ───────────────────────── -->
        <section v-if="featuredProducts?.length" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <p class="text-brand-500 text-xs tracking-[0.3em] uppercase mb-2">Selección</p>
                    <h2 class="font-display text-3xl lg:text-4xl text-stone-900">Destacados</h2>
                </div>
                <Link :href="route('products.index', { destacados: true })"
                    class="hidden sm:flex items-center gap-2 text-sm text-stone-500 hover:text-stone-900 transition-colors">
                    Ver todos
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                    </svg>
                </Link>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-10">
                <ProductCard
                    v-for="product in featuredProducts"
                    :key="product.id"
                    :product="product"
                />
            </div>
        </section>

        <!-- ── Banner intermedio ──────────────────────────── -->
        <section class="bg-brand-50 border-y border-brand-100 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-brand-600 text-xs tracking-[0.3em] uppercase mb-3">Beneficios</p>
                <h2 class="font-display text-3xl text-stone-900">¿Por qué elegirnos?</h2>
                <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-8">
                    <div v-for="benefit in [
                        { icon: '🚚', title: 'Envío gratis', desc: 'En compras mayores a $50.000' },
                        { icon: '↩️', title: 'Devolución fácil', desc: '30 días para cambios sin preguntas' },
                        { icon: '🔒', title: 'Pago seguro', desc: 'MercadoPago y tarjetas aceptadas' },
                    ]" :key="benefit.title" class="flex flex-col items-center gap-3">
                        <span class="text-3xl">{{ benefit.icon }}</span>
                        <h3 class="font-medium text-stone-900">{{ benefit.title }}</h3>
                        <p class="text-sm text-stone-500">{{ benefit.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Nuevos ingresos ────────────────────────────── -->
        <section v-if="newArrivals?.length" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <p class="text-brand-500 text-xs tracking-[0.3em] uppercase mb-2">Recién llegado</p>
                    <h2 class="font-display text-3xl lg:text-4xl text-stone-900">Nuevos ingresos</h2>
                </div>
                <Link :href="route('products.index')"
                    class="hidden sm:flex items-center gap-2 text-sm text-stone-500 hover:text-stone-900 transition-colors">
                    Ver catálogo
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                    </svg>
                </Link>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-10">
                <ProductCard
                    v-for="product in newArrivals"
                    :key="product.id"
                    :product="product"
                />
            </div>
        </section>
    </div>
</template>
