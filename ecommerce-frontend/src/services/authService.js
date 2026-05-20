import api from './api'
import { API_URL } from '../utils/constants'

const authService = {
  // تسجيل الدخول
  login: async (email, password) => {
    try {
      const response = await api.post(`${API_URL}/login`, {email, password})
      return {
        success: response.data.success,
        data: response.data.data,
        message: response.data.message,
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'فشل في تسجيل الدخول',
        errors: error.response?.data?.errors,
      }
    }
  },

  // تسجيل مستخدم جديد
  register: async (name, email, password, passwordConfirmation) => {
    try {
      const response = await api.post(`${API_URL}/register`, {
        name,
        email,
        password,
        password_confirmation: passwordConfirmation,
      })
      return {
        success: response.data.success,
        data: response.data.data,
        message: response.data.message,
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'فشل في التسجيل',
        errors: error.response?.data?.errors,
      }
    }
  },

  // تسجيل الخروج
  logout: async () => {
    try {
      const response = await api.post(`${API_URL}/logout`)
      return {
        success: response.data.success,
        message: response.data.message,
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'فشل في تسجيل الخروج',
      }
    }
  },
}

export default authService