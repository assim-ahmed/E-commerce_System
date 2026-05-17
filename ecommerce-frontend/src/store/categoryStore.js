import { create } from 'zustand'
import categoryService from '../services/categoryService'

const useCategoryStore = create((set, get) => ({
  categories: [],
  selectedCategory: null,
  categoryProducts: [],
  isLoading: false,
  error: null,
  meta: {
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
  },

  // جلب جميع التصنيفات
  fetchCategories: async () => {
    set({ isLoading: true, error: null })
    
    const result = await categoryService.getCategories()
    
    if (result.success) {
      set({
        categories: result.data,
        isLoading: false,
      })
    } else {
      set({
        error: result.message,
        isLoading: false,
      })
    }
  },

  // جلب تصنيف محدد
  fetchCategoryById: async (id) => {
    set({ isLoading: true, error: null })
    
    const result = await categoryService.getCategoryById(id)
    
    if (result.success) {
      set({
        selectedCategory: result.data,
        isLoading: false,
      })
    } else {
      set({
        error: result.message,
        isLoading: false,
      })
    }
  },

  // جلب منتجات تصنيف محدد
  fetchCategoryProducts: async (id, page = 1, perPage = 15) => {
    set({ isLoading: true, error: null })
    
    const result = await categoryService.getCategoryProducts(id, page, perPage)
    
    if (result.success) {
      set({
        categoryProducts: result.data,
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

  // تغيير الصفحة
  changePage: (page) => {
    const { meta, selectedCategory, fetchCategoryProducts } = get()
    if (selectedCategory && page >= 1 && page <= meta.last_page) {
      fetchCategoryProducts(selectedCategory.id, page, meta.per_page)
    }
  },

  // إعادة تعيين الحالة عند مغادرة الصفحة
  reset: () => {
    set({
      selectedCategory: null,
      categoryProducts: [],
      meta: {
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
      },
    })
  },
}))

export default useCategoryStore