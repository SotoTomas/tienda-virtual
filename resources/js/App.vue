<script setup>
import { onMounted } from 'vue'
import { useCatalogStore } from '@/stores/catalog'

const catalog = useCatalogStore()

onMounted(() => {
    catalog.load()
})
</script>

<template>
    <router-view />
</template>
