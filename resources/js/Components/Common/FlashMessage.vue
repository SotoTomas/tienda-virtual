<script setup>
import { storeToRefs } from 'pinia'
import { useToastStore } from '@/stores/toast'

const toast = useToastStore()
const { visible, type, message } = storeToRefs(toast)
</script>

<template>
    <Transition name="flash">
        <div v-if="visible"
            class="fixed top-6 right-6 z-50 flex items-center gap-3 px-5 py-4 shadow-lg max-w-sm"
            :class="type === 'success' ? 'bg-stone-900 text-white' : 'bg-red-600 text-white'">
            <svg v-if="type === 'success'" class="w-4 h-4 shrink-0 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <svg v-else class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span class="text-sm">{{ message }}</span>
            <button @click="toast.hide()" class="ml-2 opacity-60 hover:opacity-100">
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
