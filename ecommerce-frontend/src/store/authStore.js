import { create } from 'zustand'
import { persist } from 'zustand/middleware'
import authService from '../services/authService'
import { TOKEN_KEY } from '../utils/constants'

const useAuthStore = create(
    persist(
        (set, get) => ({
            user: null,
            token: null,
            isAuthenticated: false,
            isLoading: false,
            isLoggingOut: false,  // أضف هذا السطر
            error: null,

            // تسجيل الدخول
            login: async (email, password, rememberMe = false, fetchCart) => {
                set({ isLoading: true, error: null })

                const result = await authService.login(email, password)

                if (result.success) {
                    const { user, token } = result.data

                    if (rememberMe) {
                        localStorage.setItem(TOKEN_KEY, token)
                    }

                    set({
                        user,
                        token,
                        isAuthenticated: true,
                        isLoading: false,
                        error: null,
                    })

                    // مسح السلة المحلية أولاً
                    if (resetCart) {
                        resetCart()
                    }

                    // ثم جلب السلة الجديدة من الخادم
                    if (fetchCart) {
                        await fetchCart()
                    }

                    return { success: true, message: result.message }
                } else {
                    set({
                        isLoading: false,
                        error: result.message,
                    })
                    return { success: false, message: result.message }
                }
            },

            // تسجيل مستخدم جديد
            register: async (name, email, password, passwordConfirmation) => {
                set({ isLoading: true, error: null })

                const result = await authService.register(name, email, password, passwordConfirmation)

                if (result.success) {
                    set({
                        isLoading: false,
                        error: null,
                    })
                    return { success: true, message: result.message }
                } else {
                    set({
                        isLoading: false,
                        error: result.message,
                    })
                    return { success: false, message: result.message }
                }
            },

            // تسجيل الخروج
            logout: async () => {
                set({ isLoggingOut: true })  // أضف هذا السطر

                await authService.logout()

                localStorage.removeItem(TOKEN_KEY)

                set({
                    user: null,
                    token: null,
                    isAuthenticated: false,
                    isLoggingOut: false,  // أضف هذا السطر
                    error: null,
                })
            },

            // مسح الخطأ
            clearError: () => set({ error: null }),
        }),
        {
            name: 'auth-storage',
            partialize: (state) => ({
                user: state.user,
                token: state.token,
                isAuthenticated: state.isAuthenticated,
            }),
        }
    )
)

export default useAuthStore