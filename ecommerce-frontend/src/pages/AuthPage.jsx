import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useShallow } from 'zustand/react/shallow'
import useCartStore from '../store/cartStore'
import useAuthStore from '../store/authStore'
import LoginForm from '../components/auth/LoginForm'
import RegisterForm from '../components/auth/RegisterForm'
import { ROUTES } from '../utils/constants'

const AuthPage = () => {
    const [isLogin, setIsLogin] = useState(true)
    const navigate = useNavigate()

    const { login, register, isLoading, error, clearError } = useAuthStore(
        useShallow((state) => ({
            login: state.login,
            register: state.register,
            isLoading: state.isLoading,
            error: state.error,
            clearError: state.clearError,
        }))
    )

    const { fetchCart } = useCartStore()

    const handleLogin = async (email, password, rememberMe) => {
        const result = await login(email, password, rememberMe, fetchCart)
        if (result.success) {
            navigate(ROUTES.HOME)
        }
    }



    const handleRegister = async (name, email, password, passwordConfirmation) => {
        const result = await register(name, email, password, passwordConfirmation)
        if (result.success) {
            setIsLogin(true)
            clearError()
        }
    }

    const switchMode = () => {
        setIsLogin(!isLogin)
        clearError()
    }


    

    return (
        <div className="min-h-screen flex items-center justify-center py-12 px-4">
            <div
                className="w-full max-w-md p-8 rounded-2xl shadow-lg transition-all duration-300"
                style={{
                    backgroundColor: 'var(--color-bg-card)',
                    border: `1px solid var(--color-border-light)`,
                }}
            >
                <div className="text-center mb-8">
                    <h1 className="text-3xl font-bold" style={{ color: 'var(--color-text-primary)' }}>
                        {isLogin ? 'مرحباً بك' : 'إنشاء حساب جديد'}
                    </h1>
                    <p className="text-sm mt-2" style={{ color: 'var(--color-text-muted)' }}>
                        {isLogin ? 'سجل دخولك للمتابعة' : 'قم بإنشاء حساب للاستمرار'}
                    </p>
                </div>

                {isLogin ? (
                    <LoginForm onSubmit={handleLogin} isLoading={isLoading} error={error} />
                ) : (
                    <RegisterForm onSubmit={handleRegister} isLoading={isLoading} error={error} />
                )}

                <div className="text-center mt-6">
                    <button
                        onClick={switchMode}
                        className="text-sm hover:underline transition-colors"
                        style={{ color: 'var(--color-primary)' }}
                    >
                        {isLogin ? 'ليس لديك حساب؟ إنشاء حساب جديد' : 'لديك حساب بالفعل؟ تسجيل الدخول'}
                    </button>
                </div>
            </div>
        </div>
    )
}

export default AuthPage