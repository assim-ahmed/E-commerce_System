import { create } from 'zustand'
import cartService from '../services/cartService'

const useCartStore = create((set, get) => ({
  items: [],
  subtotal: 0,
  total: 0,
  discountAmount: 0,
  couponCode: null,
  couponType: null,
  couponValue: null,
  itemsCount: 0,
  isLoading: false,
  error: null,

  // جلب السلة
  fetchCart: async () => {
    set({ isLoading: true, error: null })
    
    const result = await cartService.getCart()
    
    if (result.success) {
      set({
        items: result.data.items || [],
        subtotal: result.data.subtotal || 0,
        total: result.data.total || 0,
        discountAmount: result.data.discount_amount || 0,
        couponCode: result.data.coupon_code || null,
        couponType: result.data.coupon_type || null,
        couponValue: result.data.coupon_value || null,
        itemsCount: result.data.items_count || 0,
        isLoading: false,
      })
    } else {
      set({
        error: result.message,
        isLoading: false,
      })
    }
  },

  // إضافة منتج
  addToCart: async (productId, quantity = 1, variantId = null) => {
    set({ isLoading: true, error: null })
    
    const result = await cartService.addToCart(productId, quantity, variantId)
    
    if (result.success) {
      await get().fetchCart()
      return { success: true, message: result.message }
    } else {
      set({ error: result.message, isLoading: false })
      return { success: false, message: result.message }
    }
  },

  // تحديث الكمية
  updateQuantity: async (itemId, quantity) => {
    set({ isLoading: true, error: null })
    
    const result = await cartService.updateQuantity(itemId, quantity)
    
    if (result.success) {
      await get().fetchCart()
      return { success: true }
    } else {
      set({ error: result.message, isLoading: false })
      return { success: false }
    }
  },

  // حذف منتج
  removeFromCart: async (itemId) => {
    set({ isLoading: true, error: null })
    
    const result = await cartService.removeFromCart(itemId)
    
    if (result.success) {
      await get().fetchCart()
      return { success: true }
    } else {
      set({ error: result.message, isLoading: false })
      return { success: false }
    }
  },

  // تفريغ السلة
  clearCart: async () => {
    set({ isLoading: true, error: null })
    
    const result = await cartService.clearCart()
    
    if (result.success) {
      set({
        items: [],
        subtotal: 0,
        total: 0,
        discountAmount: 0,
        couponCode: null,
        couponType: null,
        couponValue: null,
        itemsCount: 0,
        isLoading: false,
      })
      return { success: true }
    } else {
      set({ error: result.message, isLoading: false })
      return { success: false }
    }
  },

  // حساب عدد المنتجات
  getItemsCount: () => {
    const { items } = get()
    return items.reduce((count, item) => count + item.quantity, 0)
  },
}))

export default useCartStore