import { Navigate } from 'react-router-dom'
import { useShallow } from 'zustand/react/shallow'
import useAuthStore from '../../store/authStore'

const ProtectedRoute = ({ children, allowedRoles = [], redirectTo = '/' }) => {
  const { isAuthenticated, user } = useAuthStore(
    useShallow((state) => ({
      isAuthenticated: state.isAuthenticated,
      user: state.user,
    }))
  )

  // الحالة الأولى: المستخدم غير مسجل دخول
  if (!isAuthenticated) {
    return <Navigate to="/login" replace />
  }

  // الحالة الثانية: المستخدم مسجل ولكن دوره غير مسموح
  if (allowedRoles.length > 0 && !allowedRoles.includes(user?.role)) {
    return <Navigate to={redirectTo} replace />
  }

  // الحالة الثالثة: المستخدم مسجل ودوره مسموح
  return <>{children}</>
}

export default ProtectedRoute