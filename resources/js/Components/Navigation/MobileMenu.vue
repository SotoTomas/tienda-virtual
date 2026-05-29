<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    open:       Boolean,
    categories: Array,
})
const emit = defineEmits(['close'])
</script>

<template>
    <Teleport to="body">
        <!-- Overlay -->
        <Transition name="overlay">
            <div v-if="open" @click="emit('close')"
                class="fixed inset-0 z-50 bg-stone-900/50 backdrop-blur-sm" />
        </Transition>

        <!-- Panel -->
        <Transition name="drawer">
            <div v-if="open"
                class="fixed inset-y-0 left-0 z-50 w-80 bg-white flex flex-col shadow-xl">
                <div class="flex items-center justify-between px-6 py-5 border-b border-stone-100">
                    <span class="font-display text-xl text-stone-900">
                        Tienda<span class="text-brand-500">.</span>
                    </span>
                    <button @click="emit('close')" class="btn-ghost p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <nav class="flex-1 overflow-y-auto px-6 py-6 space-y-1">
                    <div v-for="cat in categories" :key="cat.id">
                        <Link
                            :href="route('categories.show', cat.slug)"
                            @click="emit('close')"
                            class="block py-3 text-base font-medium text-stone-800 border-b border-stone-100"
                        >
                            {{ cat.name }}
                        </Link>
                        <div v-if="cat.children?.length" class="pl-4 mt-1 space-y-1">
                            <Link
                                v-for="child in cat.children"
                                :key="child.id"
                                :href="route('categories.show', child.slug)"
                                @click="emit('close')"
                                class="block py-2 text-sm text-stone-500 hover:text-stone-900 transition-colors"
                            >
                                {{ child.name }}
                            </Link>
                        </div>
                    </div>
                    <Link :href="route('products.index')" @click="emit('close')"
                        class="block py-3 text-base font-medium text-stone-800">
                        Todo
                    </Link>
                </nav>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.overlay-enter-active, .overlay-leave-active { transition: opacity 0.3s ease; }
.overlay-enter-from, .overlay-leave-to { opacity: 0; }
.drawer-enter-active, .drawer-leave-active { transition: transform 0.3s ease; }
.drawer-enter-from, .drawer-leave-to { transform: translateX(-100%); }
</style>
