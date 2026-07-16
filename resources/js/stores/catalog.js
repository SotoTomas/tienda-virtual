import { defineStore } from 'pinia'
import { formatProductCard, formatProductDetail, paginate } from '@/utils/catalog'

let catalogPromise = null

function buildCategoryMaps(categories) {
    const bySlug = {}
    const roots = []

    for (const root of categories) {
        roots.push(root)
        bySlug[root.slug] = { ...root, parent: null }

        for (const child of root.children ?? []) {
            bySlug[child.slug] = { ...child, parent: root }
        }
    }

    return { bySlug, roots }
}

function countProductsInCategory(slug, products) {
    return products.filter((p) => p.category_slug === slug && p.is_active).length
}

function sortProducts(products, orden = 'recientes') {
    const sorted = [...products]

    switch (orden) {
        case 'precio_asc':
            return sorted.sort((a, b) => a.price - b.price)
        case 'precio_desc':
            return sorted.sort((a, b) => b.price - a.price)
        case 'nombre':
            return sorted.sort((a, b) => a.name.localeCompare(b.name, 'es'))
        default:
            return sorted.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    }
}

function filterProducts(products, filters = {}) {
    let result = products.filter((p) => p.is_active && p.stock > 0)

    if (filters.categoria) {
        result = result.filter((p) => p.category_slug === filters.categoria)
    }

    if (filters.precio_min) {
        result = result.filter((p) => p.price >= Number(filters.precio_min))
    }

    if (filters.precio_max) {
        result = result.filter((p) => p.price <= Number(filters.precio_max))
    }

    if (filters.buscar) {
        const term = filters.buscar.toLowerCase()
        result = result.filter(
            (p) =>
                p.name.toLowerCase().includes(term) ||
                p.short_description.toLowerCase().includes(term),
        )
    }

    if (filters.destacados === 'true' || filters.destacados === true) {
        result = result.filter((p) => p.is_featured)
    }

    return sortProducts(result, filters.orden)
}

export const useCatalogStore = defineStore('catalog', {
    state: () => ({
        loaded: false,
        categories: [],
        products: [],
        coupons: [],
        categoryBySlug: {},
        rootCategories: [],
    }),

    actions: {
        async load() {
            if (this.loaded) return

            if (!catalogPromise) {
                catalogPromise = Promise.all([
                    fetch('/data/categories.json').then((r) => r.json()),
                    fetch('/data/products.json').then((r) => r.json()),
                    fetch('/data/coupons.json').then((r) => r.json()),
                ])
            }

            const [categories, products, coupons] = await catalogPromise
            const { bySlug, roots } = buildCategoryMaps(categories)

            this.categories = categories
            this.products = products
            this.coupons = coupons
            this.categoryBySlug = bySlug
            this.rootCategories = roots
            this.loaded = true
        },

        async ensureLoaded() {
            if (!this.loaded) await this.load()
        },

        getNavigationCategories() {
            return this.rootCategories.map((cat) => ({
                id: cat.id,
                name: cat.name,
                slug: cat.slug,
                children: (cat.children ?? []).map((child) => ({
                    id: child.id,
                    name: child.name,
                    slug: child.slug,
                })),
            }))
        },

        getFilterCategories() {
            const items = []
            for (const root of this.rootCategories) {
                for (const child of root.children ?? []) {
                    items.push({ id: child.id, name: child.name, slug: child.slug })
                }
            }
            return items
        },

        async getFeaturedProducts(limit = 8) {
            await this.ensureLoaded()
            return this.products
                .filter((p) => p.is_featured && p.is_active && p.stock > 0)
                .slice(0, limit)
                .map((p) => formatProductCard(p, this.categoryBySlug))
        },

        async getNewArrivals(limit = 8) {
            await this.ensureLoaded()
            return sortProducts(
                this.products.filter((p) => p.is_active && p.stock > 0),
                'recientes',
            )
                .slice(0, limit)
                .map((p) => formatProductCard(p, this.categoryBySlug))
        },

        async getTopCategories(limit = 6) {
            await this.ensureLoaded()
            return this.rootCategories.slice(0, limit).map((cat) => ({
                id: cat.id,
                name: cat.name,
                slug: cat.slug,
                image: cat.image ?? null,
                products_count: (cat.children ?? []).reduce(
                    (sum, child) => sum + countProductsInCategory(child.slug, this.products),
                    0,
                ),
            }))
        },

        async getProducts(filters = {}, page = 1) {
            await this.ensureLoaded()
            const filtered = filterProducts(this.products, filters).map((p) =>
                formatProductCard(p, this.categoryBySlug),
            )
            return paginate(filtered, page)
        },

        async getProductBySlug(slug) {
            await this.ensureLoaded()
            const product = this.products.find((p) => p.slug === slug && p.is_active)
            if (!product) return null
            return formatProductDetail(product, this.categoryBySlug)
        },

        async getRelatedProducts(categorySlug, excludeId, limit = 4) {
            await this.ensureLoaded()
            return this.products
                .filter(
                    (p) =>
                        p.category_slug === categorySlug &&
                        p.id !== excludeId &&
                        p.is_active &&
                        p.stock > 0,
                )
                .slice(0, limit)
                .map((p) => formatProductCard(p, this.categoryBySlug))
        },

        async getCategoryBySlug(slug) {
            await this.ensureLoaded()
            const category = this.categoryBySlug[slug]
            if (!category) return null

            const isRoot = !category.parent
            const children = isRoot
                ? (category.children ?? []).map((child) => ({
                      id: child.id,
                      name: child.name,
                      slug: child.slug,
                  }))
                : []

            return {
                id: category.id,
                name: category.name,
                slug: category.slug,
                description: category.description ?? category.parent?.description ?? null,
                children,
                isRoot,
            }
        },

        async getCategoryProducts(slug, page = 1) {
            await this.ensureLoaded()
            const category = this.categoryBySlug[slug]
            if (!category) return paginate([], page)

            let slugs = [slug]
            const rootCategory = this.rootCategories.find((c) => c.slug === slug)
            if (rootCategory?.children?.length) {
                slugs = rootCategory.children.map((c) => c.slug)
            }

            const filtered = this.products.filter(
                (p) => slugs.includes(p.category_slug) && p.is_active && p.stock > 0,
            )

            return paginate(
                sortProducts(filtered, 'recientes').map((p) =>
                    formatProductCard(p, this.categoryBySlug),
                ),
                page,
            )
        },

        async searchProducts(query, page = 1) {
            await this.ensureLoaded()
            if (!query || query.length < 2) return paginate([], page)
            return this.getProducts({ buscar: query }, page)
        },

        findRawProduct(id) {
            return this.products.find((p) => p.id === id)
        },

        findVariant(productId, variantId) {
            const product = this.findRawProduct(productId)
            return product?.variants?.find((v) => v.id === variantId) ?? null
        },

        findCoupon(code) {
            return this.coupons.find((c) => c.code.toUpperCase() === code.toUpperCase()) ?? null
        },
    },
})
