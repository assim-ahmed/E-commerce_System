import { create } from 'zustand'
import productService from '../services/productService'

const useProductStore = create((set, get) => ({
  products: [],
  featuredProducts: [],
  isLoading: false,
  error: null,
  meta: {
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
  },

  // جلب جميع المنتجات
  fetchProducts: async (page = 1, perPage = 15) => {
    set({ isLoading: true, error: null })
    
    const result = await productService.getProducts(page, perPage)
    
    if (result.success) {
      set({
        products: result.data,
        meta: result.meta,
        isLoading: false,
      })
    } else {
      set({
        error: result.message,
        isLoading: false,
      })
    }
  },

  // جلب المنتجات المميزة
  fetchFeaturedProducts: async (limit = 10) => {
    const result = await productService.getFeaturedProducts(limit)
    
    if (result.success) {
      set({ featuredProducts: result.data })
    }
  },

  // تغيير الصفحة
  changePage: (page) => {
    const { meta, fetchProducts } = get()
    if (page >= 1 && page <= meta.last_page) {
      fetchProducts(page, meta.per_page)
    }
  },
}))

export default useProductStore