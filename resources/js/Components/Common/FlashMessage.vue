<script setup>
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page    = usePage()
const visible = ref(false)
const current = ref({ type: '', message: '' })
let timer     = null

watch(
    () => ({ ...page.props.flash }),
    (flash) => {
        if (timer) clearTimeout(timer)

        if (flash?.success) {
            current.value = { type: 'success', message: flash.success }
            visible.value = true
            timer = setTimeout(() => visible.value = false, 3500)
        } else if (flash?.error) {
            current.value = { type: 'error', message: flash.error }
            visible.value = true
            timer = setTimeout(() => visible.value = false, 4000)
        }
    }
)
</script>

<template>
    <Transition name="flash">
        <div v-if="visible"
            class="fixed top-6 right-6 z-50 flex items-center gap-3 px-5 py-4 shadow-lg max-w-sm"
            :class="current.type === 'success' ? 'bg-stone-900 text-white' : 'bg-red-600 text-white'">
            <svg v-if="current.type === 'success'" class="w-4 h-4 shrink-0 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <svg v-else class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span class="text-sm">{{ current.message }}</span>
            <button @click="visible = false" class="ml-2 opacity-60 hover:opacity-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </Transition>
</template>

<style scoped>
.flash-enter-active, .flash-leave-active { transition: all 0.3s ease; }
.flash-enter-from, .flash-leave-to { opacity: 0; transform: translateX(20px); }
</style>
