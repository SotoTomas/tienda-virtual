tienda-virtual/
│
├── app/
│   ├── Actions/                        # Acciones de negocio (single-purpose classes)
│   │   ├── Cart/
│   │   │   ├── AddItemToCart.php
│   │   │   ├── RemoveItemFromCart.php
│   │   │   ├── UpdateCartItemQuantity.php
│   │   │   └── MergeGuestCartOnLogin.php
│   │   ├── Order/
│   │   │   ├── CreateOrderFromCart.php
│   │   │   ├── CancelOrder.php
│   │   │   └── GenerateOrderNumber.php
│   │   └── Payment/
│   │       ├── ProcessMercadoPagoPayment.php
│   │       └── HandlePaymentWebhook.php
│   │
│   ├── Enums/                          # Enumeraciones (PHP 8.1+)
│   │   ├── OrderStatus.php             # pending, confirmed, processing, shipped...
│   │   ├── PaymentStatus.php           # unpaid, paid, refunded...
│   │   └── CouponType.php              # percentage, fixed
│   │
│   ├── Events/                         # Eventos del dominio
│   │   ├── OrderPlaced.php
│   │   ├── OrderStatusChanged.php
│   │   └── PaymentConfirmed.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                  # Panel de administración
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── OrderController.php
│   │   │   │   ├── CouponController.php
│   │   │   │   └── ReviewController.php
│   │   │   │
│   │   │   ├── Store/                  # Tienda pública
│   │   │   │   ├── HomeController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   └── SearchController.php
│   │   │   │
│   │   │   ├── CartController.php
│   │   │   ├── CheckoutController.php
│   │   │   ├── OrderController.php
│   │   │   ├── WishlistController.php
│   │   │   ├── ReviewController.php
│   │   │   └── WebhookController.php   # Webhooks de pasarelas de pago
│   │   │
│   │   ├── Middleware/
│   │   │   ├── HandleInertiaRequests.php   # Comparte datos globales (cart, user, etc.)
│   │   │   └── EnsureIsAdmin.php
│   │   │
│   │   └── Requests/                   # Form Requests validados
│   │       ├── StoreProductRequest.php
│   │       ├── CheckoutRequest.php
│   │       ├── StoreReviewRequest.php
│   │       └── ApplyCouponRequest.php
│   │
│   ├── Listeners/                      # Manejadores de eventos
│   │   ├── SendOrderConfirmationEmail.php
│   │   ├── UpdateProductStock.php
│   │   └── NotifyAdminOnNewOrder.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── ProductImage.php
│   │   ├── ProductVariant.php
│   │   ├── Cart.php
│   │   ├── CartItem.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Address.php
│   │   ├── Coupon.php
│   │   ├── Review.php
│   │   └── Wishlist.php
│   │
│   ├── Notifications/
│   │   ├── OrderPlacedNotification.php
│   │   ├── OrderShippedNotification.php
│   │   └── PasswordResetNotification.php
│   │
│   ├── Observers/                      # Observers de modelos
│   │   ├── ProductObserver.php         # Ej: auto-generar slug
│   │   └── OrderObserver.php           # Ej: generar número de orden
│   │
│   ├── Policies/
│   │   ├── OrderPolicy.php
│   │   └── ReviewPolicy.php
│   │
│   ├── Services/                       # Servicios de infraestructura
│   │   ├── CartService.php
│   │   ├── PaymentService.php
│   │   ├── MercadoPagoService.php
│   │   ├── ShippingService.php
│   │   └── ImageService.php            # Upload, resize, optimización
│   │
│   └── Traits/
│       ├── HasSlug.php
│       └── HasFilters.php
│
├── bootstrap/
│   └── app.php
│
├── config/
│   ├── mercadopago.php                 # Config propia para MercadoPago
│   └── store.php                       # Config general (nombre tienda, moneda, etc.)
│
├── database/
│   ├── factories/
│   │   ├── ProductFactory.php
│   │   ├── CategoryFactory.php
│   │   └── OrderFactory.php
│   ├── migrations/
│   │   ├── create_categories_table.php
│   │   ├── create_products_table.php
│   │   ├── create_product_images_table.php
│   │   ├── create_product_variants_table.php
│   │   ├── create_addresses_table.php
│   │   ├── create_orders_table.php
│   │   ├── create_order_items_table.php
│   │   ├── create_carts_table.php
│   │   ├── create_cart_items_table.php
│   │   ├── create_coupons_table.php
│   │   ├── create_reviews_table.php
│   │   └── create_wishlists_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── CategorySeeder.php
│       ├── ProductSeeder.php
│       └── AdminUserSeeder.php
│
├── resources/
│   ├── js/
│   │   ├── app.js                      # Entry point (Inertia + Pinia setup)
│   │   │
│   │   ├── Components/                 # Componentes reutilizables
│   │   │   ├── Common/
│   │   │   │   ├── AppButton.vue
│   │   │   │   ├── AppModal.vue
│   │   │   │   ├── AppBadge.vue
│   │   │   │   ├── AppPagination.vue
│   │   │   │   ├── AppAlert.vue
│   │   │   │   └── AppSpinner.vue
│   │   │   │
│   │   │   ├── Product/
│   │   │   │   ├── ProductCard.vue
│   │   │   │   ├── ProductGrid.vue
│   │   │   │   ├── ProductGallery.vue
│   │   │   │   ├── ProductRating.vue
│   │   │   │   ├── ProductVariantSelector.vue
│   │   │   │   └── ProductFilters.vue
│   │   │   │
│   │   │   ├── Cart/
│   │   │   │   ├── CartDrawer.vue      # Sidebar/offcanvas del carrito
│   │   │   │   ├── CartItem.vue
│   │   │   │   ├── CartSummary.vue
│   │   │   │   └── CartBadge.vue       # Ícono con contador
│   │   │   │
│   │   │   ├── Checkout/
│   │   │   │   ├── AddressForm.vue
│   │   │   │   ├── PaymentMethod.vue
│   │   │   │   ├── OrderSummary.vue
│   │   │   │   └── CouponInput.vue
│   │   │   │
│   │   │   └── Navigation/
│   │   │       ├── Navbar.vue
│   │   │       ├── MobileMenu.vue
│   │   │       ├── SearchBar.vue
│   │   │       └── CategoryMenu.vue
│   │   │
│   │   ├── Layouts/
│   │   │   ├── StoreLayout.vue         # Layout principal tienda
│   │   │   ├── AdminLayout.vue         # Layout panel admin
│   │   │   └── AuthLayout.vue          # Login / Register
│   │   │
│   │   ├── Pages/                      # Páginas Inertia (una por ruta)
│   │   │   ├── Auth/
│   │   │   │   ├── Login.vue
│   │   │   │   ├── Register.vue
│   │   │   │   └── ForgotPassword.vue
│   │   │   │
│   │   │   ├── Store/
│   │   │   │   ├── Home.vue
│   │   │   │   ├── Products/
│   │   │   │   │   ├── Index.vue       # Catálogo con filtros
│   │   │   │   │   └── Show.vue        # Detalle de producto
│   │   │   │   ├── Category/
│   │   │   │   │   └── Show.vue
│   │   │   │   └── Search.vue
│   │   │   │
│   │   │   ├── Cart/
│   │   │   │   └── Index.vue
│   │   │   │
│   │   │   ├── Checkout/
│   │   │   │   ├── Index.vue
│   │   │   │   └── Success.vue
│   │   │   │
│   │   │   ├── Account/
│   │   │   │   ├── Profile.vue
│   │   │   │   ├── Orders/
│   │   │   │   │   ├── Index.vue
│   │   │   │   │   └── Show.vue
│   │   │   │   ├── Addresses.vue
│   │   │   │   └── Wishlist.vue
│   │   │   │
│   │   │   └── Admin/
│   │   │       ├── Dashboard.vue
│   │   │       ├── Products/
│   │   │       │   ├── Index.vue
│   │   │       │   ├── Create.vue
│   │   │       │   └── Edit.vue
│   │   │       ├── Categories/
│   │   │       │   ├── Index.vue
│   │   │       │   └── Form.vue
│   │   │       ├── Orders/
│   │   │       │   ├── Index.vue
│   │   │       │   └── Show.vue
│   │   │       ├── Coupons/
│   │   │       │   └── Index.vue
│   │   │       └── Reviews/
│   │   │           └── Index.vue
│   │   │
│   │   ├── Stores/                     # Pinia stores
│   │   │   ├── useCartStore.js
│   │   │   ├── useWishlistStore.js
│   │   │   └── useUiStore.js           # Modals, sidebar, toasts
│   │   │
│   │   └── Composables/                # Composables Vue 3
│   │       ├── useCart.js
│   │       ├── useFilters.js
│   │       ├── useToast.js
│   │       └── useFormatPrice.js
│   │
│   └── css/
│       └── app.css                     # Tailwind base
│
├── routes/
│   ├── web.php                         # Rutas públicas tienda + auth
│   ├── admin.php                       # Rutas del panel admin
│   └── webhooks.php                    # Webhooks de pago
│
├── tests/
│   ├── Feature/
│   │   ├── Cart/
│   │   │   └── CartTest.php
│   │   ├── Checkout/
│   │   │   └── CheckoutTest.php
│   │   └── Admin/
│   │       └── ProductTest.php
│   └── Unit/
│       ├── CartServiceTest.php
│       └── CouponTest.php
│
├── .env
├── .env.example
├── .gitignore
├── composer.json
├── package.json
├── tailwind.config.js
└── vite.config.js