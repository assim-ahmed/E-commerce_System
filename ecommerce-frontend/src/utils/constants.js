// رابط السيرفر الأساسي
export const API_URL = 'http://localhost:8000/api'

// رابط تخزين الصور
export const STORAGE_URL = 'http://localhost:8000/storage'

// مفاتيح التخزين المحلي
export const TOKEN_KEY = 'auth_token'
export const USER_KEY = 'user_data'
export const CART_COOKIE_NAME = 'cart_cookie'

// إعدادات الصفحات
export const DEFAULT_PAGE = 1
export const DEFAULT_PER_PAGE = 15

// روابط الصفحات (Frontend - يستخدمها الـ Navbar)
export const ROUTES = {
  HOME: '/',
  PRODUCTS: '/products',
  PRODUCT_DETAILS: '/product/:id',
  FEATURED_PRODUCTS: '/products/featured',
  CART: '/cart',
  LOGIN: '/login',
  REGISTER: '/register',
  PROFILE: '/profile',
  ORDERS: '/orders',
  CATEGORIES: '/categories',
  ADMIN_DASHBOARD: '/admin/dashboard',
  ADMIN_PRODUCTS: '/admin/products',
  ADMIN_CATEGORIES: '/admin/categories',
  ADMIN_BRANDS: '/admin/brands',
  ADMIN_COUPONS: '/admin/coupons',
  ADMIN_ORDERS: '/admin/orders',
  ADMIN_REVIEWS: '/admin/reviews',
}