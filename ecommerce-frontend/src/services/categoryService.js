import api from './api'
import { API_URL } from '../utils/constants'

const categoryService = {
  // جلب جميع التصنيفات
  getCategories: async () => {
    try {
      const response = await api.get(`${API_URL}/categories`)
      return {
        success: response.data.success,
        data: response.data.data,
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'فشل في جلب التصنيفات',
      }
    }
  },

  // جلب تصنيف واحد بالـ ID
  getCategoryById: async (id) => {
    try {
      const response = await api.get(`${API_URL}/categories/${id}`)
      return {
        success: response.data.success,
        data: response.data.data,
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'فشل في جلب التصنيف',
      }
    }
  },

  // جلب منتجات تصنيف معين
  getCategoryProducts: async (id, page = 1, perPage = 15) => {
    try {
      const response = await api.get(`${API_URL}/products?category_id=${id}&page=${page}&per_page=${perPage}`)
      return {
        success: response.data.success,
        data: response.data.data,
        meta: response.data.meta,
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'فشل في جلب منتجات التصنيف',
      }
    }
  },
}

export default categoryService