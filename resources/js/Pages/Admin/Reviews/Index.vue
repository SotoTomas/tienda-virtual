<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: AdminLayout })

defineProps({
    reviews: Object,
})

function approve(id) {
    router.patch(route('admin.reviews.approve', id), {}, { preserveScroll: true })
}

function destroy(id) {
    if (!confirm('¿Eliminar esta reseña?')) return
    router.delete(route('admin.reviews.destroy', id), { preserveScroll: true })
}
</script>

<template>
    <div>
        <div class="mb-8">
            <h1 class="font-display text-3xl text-stone-900">Reseñas</h1>
            <p class="text-stone-500 text-sm mt-1">Moderá las reseñas de los clientes</p>
        </div>

        <div class="space-y-4">
            <div v-for="review in reviews.data" :key="review.id"
                class="bg-white border rounded p-6"
                :class="review.is_approved ? 'border-stone-100' : 'border-orange-200 bg-orange-50'">

                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="text-sm font-medium text-stone-800">{{ review.user_name }}</span>
                            <div class="flex">
                                <span v-for="i in 5" :key="i" class="text-xs"
                                    :class="i <= review.rating ? 'text-brand-500' : 'text-stone-200'">★</span>
                            </div>
                            <span class="text-xs text-stone-400">{{ review.created_at }}</span>
                            <span v-if="!review.is_approved" class="badge bg-orange-100 text-orange-600">
                                Pendiente
                            </span>
                            <span v-else class="badge bg-green-100 text-green-600">
                                Aprobada
                            </span>
                        </div>
                        <p class="text-xs text-stone-400 mt-1">
                            Producto: <span class="text-stone-600">{{ review.product_name }}</span>
                        </p>
                        <p v-if="review.title" class="text-sm font-medium text-stone-700 mt-3">{{ review.title }}</p>
                        <p class="text-sm text-stone-600 mt-1">{{ review.body }}</p>
                    </div>

                    <div class="flex flex-col gap-2 shrink-0">
                        <button v-if="!review.is_approved" @click="approve(review.id)"
                            class="btn-primary px-4 py-1.5 text-xs">
                            Aprobar
                        </button>
                        <button @click="destroy(review.id)"
                            class="btn-secondary px-4 py-1.5 text-xs border-red-200 text-red-500 hover:border-red-400">
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="!reviews.data?.length" class="py-24 text-center text-stone-400">
                <p class="text-sm">No hay reseñas pendientes de moderación.</p>
            </div>
        </div>
    </div>
</template>
