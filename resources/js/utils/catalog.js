export function productImage(path) {
    if (!path) return null
    if (path.startsWith('http://') || path.startsWith('https://')) return path
    return `/${path.replace(/^\//, '')}`
}

export function formatProductCard(product, categoriesBySlug) {
    const category = categoriesBySlug?.[product.category_slug]
    const hasDiscount = product.compare_price && product.compare_price > product.price
    const discountPercentage = hasDiscount
        ? Math.round((1 - product.price / product.compare_price) * 100)
        : 0

    return {
        id: product.id,
        name: product.name,
        slug: product.slug,
        short_description: product.short_description,
        price: product.price,
        compare_price: product.compare_price,
        has_discount: hasDiscount,
        discount_percentage: discountPercentage,
        main_image: product.main_image,
        is_in_stock: product.stock > 0,
        category: category?.name ?? null,
    }
}

export function formatProductDetail(product, categoriesBySlug) {
    const category = categoriesBySlug?.[product.category_slug]
    const hasDiscount = product.compare_price && product.compare_price > product.price
    const discountPercentage = hasDiscount
        ? Math.round((1 - product.price / product.compare_price) * 100)
        : 0
    const ratings = product.reviews?.map((r) => r.rating) ?? []
    const averageRating = ratings.length
        ? Math.round((ratings.reduce((a, b) => a + b, 0) / ratings.length) * 10) / 10
        : 0

    return {
        id: product.id,
        name: product.name,
        slug: product.slug,
        short_description: product.short_description,
        description: product.description,
        price: product.price,
        compare_price: product.compare_price,
        has_discount: hasDiscount,
        discount_percentage: discountPercentage,
        main_image: product.main_image,
        is_in_stock: product.stock > 0,
        stock: product.stock,
        average_rating: averageRating,
        sku: product.sku,
        category: {
            name: category?.name ?? '',
            slug: category?.slug ?? product.category_slug,
        },
        variants: (product.variants ?? []).map((v) => ({
            id: v.id,
            name: v.name,
            options: v.options,
            price: v.price ?? product.price,
            stock: v.stock,
            in_stock: v.stock > 0,
        })),
        images: product.images ?? [],
        reviews: product.reviews ?? [],
    }
}

export function paginate(items, page = 1, perPage = 12) {
    const total = items.length
    const lastPage = Math.max(1, Math.ceil(total / perPage))
    const currentPage = Math.min(Math.max(1, Number(page) || 1), lastPage)
    const data = items.slice((currentPage - 1) * perPage, currentPage * perPage)

    return {
        data,
        total,
        current_page: currentPage,
        last_page: lastPage,
        per_page: perPage,
    }
}
