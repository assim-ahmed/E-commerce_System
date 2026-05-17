import api from './api'
import { API_URL } from '../utils/constants'

const productService = {
  // جلب جميع المنتجات
  getProducts: async (page = 1, perPage = 15) => {
    try {
      const response = await api.get(`${API_URL}/products?page=${page}&per_page=${perPage}`)
      return {
        success: true,
        data: response.data.data,
        meta: response.data.meta,
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'فشل في جلب المنتجات',
      }
    }
  },

  // جلب المنتجات المميزة
  getFeaturedProducts: async (limit = 10) => {
    try {
      const response = await api.get(`${API_URL}/products/featured?limit=${limit}`)
      return {
        success: true,
        data: response.data.data,
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'فشل في جلب المنتجات المميزة',
      }
    }
  },
}

export default productService