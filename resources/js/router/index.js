import { createRouter, createWebHistory } from 'vue-router'
import StoreLayout from '@/Layouts/StoreLayout.vue'

const router = createRouter({
    history: createWebHistory(),
    scrollBehavior() {
        return { top: 0 }
    },
    routes: [
        {
            path: '/',
            component: StoreLayout,
            children: [
                {
                    path: '',
                    name: 'home',
                    component: () => import('@/Pages/Store/Home.vue'),
                },
                {
                    path: 'productos',
                    name: 'products.index',
                    component: () => import('@/Pages/Store/Products/Index.vue'),
                },
                {
                    path: 'productos/:slug',
                    name: 'products.show',
                    component: () => import('@/Pages/Store/Products/Show.vue'),
                },
                {
                    path: 'categoria/:slug',
                    name: 'categories.show',
                    component: () => import('@/Pages/Store/Category/Show.vue'),
                },
                {
                    path: 'buscar',
                    name: 'search',
                    component: () => import('@/Pages/Store/Search.vue'),
                },
                {
                    path: 'carrito',
                    name: 'cart.index',
                    component: () => import('@/Pages/Cart/Index.vue'),
                },
            ],
        },
        {
            path: '/:pathMatch(.*)*',
            name: 'not-found',
            component: () => import('@/Pages/Store/NotFound.vue'),
        },
    ],
})

export default router
