import { Navigate } from 'react-router-dom'
import { useShallow } from 'zustand/react/shallow'
import useAuthStore from '../../store/authStore'

const GuestPage = ({ children }) => {
  const { isAuthenticated } = useAuthStore(
    useShallow((state) => ({
      isAuthenticated: state.isAuthenticated,
    }))
  )

  // إذا كان المستخدم مسجلاً دخول → اذهب إلى الصفحة الرئيسية
  if (isAuthenticated) {
    return <Navigate to="/" replace />
  }

  // إذا كان غير مسجل → اعرض الصفحة (تسجيل الدخول أو التسجيل)
  return <>{children}</>
}

export default GuestPage