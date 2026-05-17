import api from './api'
import { API_URL } from '../utils/constants'

const cartService = {
  // جلب السلة
  getCart: async () => {
    try {
      const response = await api.get(`${API_URL}/cart`)
      return {
        success: response.data.success,
        data: response.data.data,
        message: response.data.message,
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'فشل في جلب السلة',
      }
    }
  },

  // إضافة منتج إلى السلة
  addToCart: async (productId, quantity = 1, variantId = null) => {
    try {
      const response = await api.post(`${API_URL}/cart/items`, {
        product_id: productId,
        quantity: quantity,
        product_variant_id: variantId,
      })
      return {
        success: response.data.success,
        data: response.data.data,
        message: response.data.message,
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'فشل في إضافة المنتج',
      }
    }
  },

  // تحديث كمية منتج في السلة
  updateQuantity: async (itemId, quantity) => {
    try {
      const response = await api.put(`${API_URL}/cart/items/${itemId}`, { quantity })
      return {
        success: response.data.success,
        data: response.data.data,
        message: response.data.message,
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'فشل في تحديث الكمية',
      }
    }
  },

  // حذف منتج من السلة
  removeFromCart: async (itemId) => {
    try {
      const response = await api.delete(`${API_URL}/cart/items/${itemId}`)
      return {
        success: response.data.success,
        message: response.data.message,
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'فشل في حذف المنتج',
      }
    }
  },

  // تفريغ السلة
  clearCart: async () => {
    try {
      const response = await api.delete(`${API_URL}/cart/clear`)
      return {
        success: response.data.success,
        message: response.data.message,
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'فشل في تفريغ السلة',
      }
    }
  },
}

export default cartService