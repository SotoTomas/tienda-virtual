<script setup>
import { RouterLink } from 'vue-router'
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 text-center">
        <h1 class="font-display text-4xl text-stone-900 mb-4">Página no encontrada</h1>
        <p class="text-stone-500 mb-8">La ruta que buscás no existe.</p>
        <RouterLink to="/" class="btn-primary px-8 py-3 tracking-widest uppercase text-xs">
            Volver al inicio
        </RouterLink>
    </div>
</template>
