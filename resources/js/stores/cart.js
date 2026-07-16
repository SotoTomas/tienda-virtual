import { defineStore } from 'pinia'
import { formatProductCard } from '@/utils/catalog'
import { useCatalogStore } from '@/stores/catalog'
import { useToastStore } from '@/stores/toast'

const STORAGE_KEY = 'tienda-cart'

function loadFromStorage() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY)
        return raw ? JSON.parse(raw) : { items: [], couponCode: null }
    } catch {
        return { items: [], couponCode: null }
    }
}

function saveToStorage(state) {
    localStorage.setItem(
        STORAGE_KEY,
        JSON.stringify({ items: state.items, couponCode: state.couponCode }),
    )
}

function nextItemId(items) {
    return items.length ? Math.max(...items.map((i) => i.id)) + 1 : 1
}

export const useCartStore = defineStore('cart', {
    state: () => {
        const saved = loadFromStorage()
        return {
            items: saved.items,
            couponCode: saved.couponCode,
        }
    },

    getters: {
        count(state) {
            return state.items.reduce((sum, item) => sum + item.quantity, 0)
        },

        itemCount(state) {
            return state.items.length
        },

        subtotal(state) {
            return state.items.reduce((sum, item) => sum + item.unit_price * item.quantity, 0)
        },

        couponDiscount(state) {
            if (!state.couponCode) return 0
            const catalog = useCatalogStore()
            const coupon = catalog.findCoupon(state.couponCode)
            if (!coupon) return 0

            const subtotal = state.items.reduce(
                (sum, item) => sum + item.unit_price * item.quantity,
                0,
            )
            if (subtotal < (coupon.min_purchase ?? 0)) return 0

            if (coupon.type === 'percentage') {
                return Math.round(subtotal * (coupon.value / 100))
            }

            return Math.min(coupon.value, subtotal)
        },

        total() {
            return Math.max(0, this.subtotal - this.couponDiscount)
        },

        cartView(state) {
            const catalog = useCatalogStore()
            const items = state.items.map((item) => {
                const raw = catalog.findRawProduct(item.product_id)
                const variant = item.variant_id
                    ? catalog.findVariant(item.product_id, item.variant_id)
                    : null
                const product = raw
                    ? formatProductCard(raw, catalog.categoryBySlug)
                    : item.product_snapshot

                const unitPrice = variant?.price ?? raw?.price ?? item.unit_price
                const subtotal = unitPrice * item.quantity

                return {
                    id: item.id,
                    quantity: item.quantity,
                    unit_price: unitPrice,
                    subtotal,
                    product: {
                        ...product,
                        stock: variant?.stock ?? raw?.stock ?? 99,
                    },
                    variant: variant
                        ? { id: variant.id, name: variant.name }
                        : null,
                }
            })

            const subtotal = items.reduce((sum, item) => sum + item.subtotal, 0)
            const couponDiscount = (() => {
                if (!state.couponCode) return 0
                const coupon = catalog.findCoupon(state.couponCode)
                if (!coupon || subtotal < (coupon.min_purchase ?? 0)) return 0
                if (coupon.type === 'percentage') {
                    return Math.round(subtotal * (coupon.value / 100))
                }
                return Math.min(coupon.value, subtotal)
            })()

            return {
                count: items.reduce((sum, item) => sum + item.quantity, 0),
                item_count: items.length,
                items,
                subtotal,
                coupon_code: state.couponCode,
                coupon_discount: couponDiscount,
                total: Math.max(0, subtotal - couponDiscount),
            }
        },
    },

    actions: {
        persist() {
            saveToStorage(this)
        },

        async addItem(productId, quantity = 1, variantId = null) {
            const catalog = useCatalogStore()
            await catalog.ensureLoaded()

            const raw = catalog.findRawProduct(productId)
            if (!raw || raw.stock <= 0) return

            const variant = variantId ? catalog.findVariant(productId, variantId) : null
            if (variantId && (!variant || variant.stock <= 0)) return

            const unitPrice = variant?.price ?? raw.price
            const existing = this.items.find(
                (i) => i.product_id === productId && i.variant_id === variantId,
            )

            if (existing) {
                existing.quantity = Math.min(existing.quantity + quantity, 10)
            } else {
                this.items.push({
                    id: nextItemId(this.items),
                    product_id: productId,
                    variant_id: variantId,
                    quantity: Math.min(quantity, 10),
                    unit_price: unitPrice,
                    product_snapshot: formatProductCard(raw, catalog.categoryBySlug),
                })
            }

            this.persist()
            useToastStore().show('Producto agregado al carrito', 'success')
        },

        updateQuantity(itemId, quantity) {
            const item = this.items.find((i) => i.id === itemId)
            if (!item || quantity < 1) return
            item.quantity = Math.min(quantity, 10)
            this.persist()
        },

        removeItem(itemId) {
            this.items = this.items.filter((i) => i.id !== itemId)
            this.persist()
        },

        async applyCoupon(code) {
            const catalog = useCatalogStore()
            await catalog.ensureLoaded()

            const coupon = catalog.findCoupon(code)
            const toast = useToastStore()

            if (!coupon) {
                toast.show('Cupón inválido', 'error')
                return false
            }

            if (this.subtotal < (coupon.min_purchase ?? 0)) {
                toast.show(
                    `Este cupón requiere una compra mínima de $${coupon.min_purchase.toLocaleString('es-AR')}`,
                    'error',
                )
                return false
            }

            this.couponCode = coupon.code
            this.persist()
            toast.show('Cupón aplicado correctamente', 'success')
            return true
        },

        removeCoupon() {
            this.couponCode = null
            this.persist()
        },

        clear() {
            this.items = []
            this.couponCode = null
            this.persist()
        },
    },
})
