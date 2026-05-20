import { useState, useEffect } from 'react'
import { useShallow } from 'zustand/react/shallow'
import { Link, useLocation, useNavigate } from 'react-router-dom'
import useCartStore from '../../store/cartStore'
import useAuthStore from '../../store/authStore'
import {
  FiShoppingCart,
  FiMoon,
  FiSun,
  FiMenu,
  FiX,
  FiHome,
  FiGrid,
  FiTag,
  FiStar,
  FiUser,
  FiLogOut,
  FiChevronDown,
  FiBarChart2
} from 'react-icons/fi'
import useDarkMode from '../../hooks/useDarkMode'
import { ROUTES } from '../../utils/constants'

const Navbar = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false)
  const [isUserMenuOpen, setIsUserMenuOpen] = useState(false)
  const { isDark, toggle } = useDarkMode()
  const location = useLocation()
  const navigate = useNavigate()

  const { user, isAuthenticated, isLoggingOut, logout } = useAuthStore(
    useShallow((state) => ({
      user: state.user,
      isAuthenticated: state.isAuthenticated,
      isLoggingOut: state.isLoggingOut,
      logout: state.logout,
    }))
  )

  const handleLogout = async () => {
    setIsUserMenuOpen(false)
    await logout()
    navigate(ROUTES.HOME)
  }

  // التحقق إذا كان المستخدم أدمن
  const isAdmin = user?.role === 'admin'

  const navLinks = [
    { name: 'الرئيسية', path: ROUTES.HOME, icon: FiHome },
    { name: 'المنتجات', path: ROUTES.PRODUCTS, icon: FiGrid },
    { name: 'التصنيفات', path: ROUTES.CATEGORIES, icon: FiTag },
    { name: 'المميزة', path: ROUTES.FEATURED_PRODUCTS, icon: FiStar },
  ]

  const getIsActive = (path) => {
    if (path === ROUTES.HOME) {
      return location.pathname === ROUTES.HOME
    }
    if (path === ROUTES.PRODUCTS) {
      return location.pathname === ROUTES.PRODUCTS
    }
    if (path === ROUTES.FEATURED_PRODUCTS) {
      return location.pathname === ROUTES.FEATURED_PRODUCTS
    }
    if (path === ROUTES.CATEGORIES) {
      return location.pathname === ROUTES.CATEGORIES
    }
    return false
  }

  const { itemsCount, fetchCart } = useCartStore()

  useEffect(() => {
    fetchCart()
  }, [])

  // إغلاق القائمة المنسدلة عند الضغط خارجها
  useEffect(() => {
    const handleClickOutside = () => {
      setIsUserMenuOpen(false)
    }
    document.addEventListener('click', handleClickOutside)
    return () => document.removeEventListener('click', handleClickOutside)
  }, [])

  return (
    <nav
      className="sticky top-0 z-50 shadow-md transition-colors duration-300"
      style={{ backgroundColor: 'var(--color-bg-navbar)' }}
    >
      <div className="container mx-auto px-4">
        <div className="flex justify-between items-center h-16">

          {/* الشعار */}
          <Link
            to={ROUTES.HOME}
            className="text-2xl font-bold transition-colors duration-300"
            style={{ color: 'var(--color-primary)' }}
          >
            بوينت
          </Link>

          {/* الروابط للشاشات الكبيرة */}
          <div className="hidden md:flex items-center gap-2">
            {navLinks.map((link) => {
              const isActive = getIsActive(link.path)
              return (
                <Link
                  key={link.path}
                  to={link.path}
                  className={`
                    flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-300
                    ${isActive ? 'bg-primary-soft text-primary-dark border border-primary' : ''}
                  `}
                  style={{
                    color: isActive ? 'var(--color-primary-dark)' : 'var(--color-text-secondary)',
                    backgroundColor: isActive ? 'var(--color-primary-soft)' : 'transparent',
                    border: isActive ? `1px solid var(--color-primary)` : `1px solid var(--color-border-light)`,
                  }}
                  onMouseEnter={(e) => {
                    if (!isActive) {
                      e.currentTarget.style.backgroundColor = 'var(--color-primary-soft)'
                      e.currentTarget.style.borderColor = 'var(--color-primary)'
                      e.currentTarget.style.color = 'var(--color-primary-dark)'
                    }
                  }}
                  onMouseLeave={(e) => {
                    if (!isActive) {
                      e.currentTarget.style.backgroundColor = 'transparent'
                      e.currentTarget.style.borderColor = 'var(--color-border-light)'
                      e.currentTarget.style.color = 'var(--color-text-secondary)'
                    }
                  }}
                >
                  <link.icon size={18} />
                  <span>{link.name}</span>
                </Link>
              )
            })}
          </div>

          {/* الأزرار الجانبية */}
          <div className="flex items-center gap-3">

            {/* زر السلة */}
            <Link
              to={ROUTES.CART}
              className={`
                relative p-2 rounded-lg transition-all duration-300
                ${location.pathname === ROUTES.CART ? 'bg-primary-soft text-primary-dark border border-primary' : ''}
              `}
              style={{
                backgroundColor: location.pathname === ROUTES.CART ? 'var(--color-primary-soft)' : 'transparent',
                color: location.pathname === ROUTES.CART ? 'var(--color-primary-dark)' : 'var(--color-text-secondary)',
                border: location.pathname === ROUTES.CART ? `1px solid var(--color-primary)` : `1px solid var(--color-border-light)`,
              }}
            >
              <FiShoppingCart size={20} />
              {itemsCount > 0 && (
                <span
                  className="absolute -top-2 -right-2 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
                  style={{ backgroundColor: 'var(--color-danger)' }}
                >
                  {itemsCount}
                </span>
              )}
            </Link>

            {/* زر تغيير الوضع */}
            <button
              onClick={toggle}
              className="p-2 rounded-lg transition-all duration-300"
              style={{
                backgroundColor: 'transparent',
                color: 'var(--color-text-secondary)',
                border: `1px solid var(--color-border-light)`,
              }}
              onMouseEnter={(e) => {
                e.currentTarget.style.backgroundColor = 'var(--color-primary-soft)'
                e.currentTarget.style.borderColor = 'var(--color-primary)'
                e.currentTarget.style.color = 'var(--color-primary-dark)'
              }}
              onMouseLeave={(e) => {
                e.currentTarget.style.backgroundColor = 'transparent'
                e.currentTarget.style.borderColor = 'var(--color-border-light)'
                e.currentTarget.style.color = 'var(--color-text-secondary)'
              }}
            >
              {isDark ? <FiSun size={20} /> : <FiMoon size={20} />}
            </button>

            {/* قسم المستخدم */}
            {isAuthenticated ? (
              <div className="relative">
                <button
                  onClick={(e) => {
                    e.stopPropagation()
                    if (!isLoggingOut) setIsUserMenuOpen(!isUserMenuOpen)
                  }}
                  disabled={isLoggingOut}
                  className="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-300 hover:shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                  style={{
                    backgroundColor: 'var(--color-primary-soft)',
                    color: 'var(--color-primary-dark)',
                    border: `1px solid var(--color-border-light)`,
                  }}
                >
                  {isLoggingOut ? (
                    <>
                      <div className="w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin" />
                      <span>جاري الخروج...</span>
                    </>
                  ) : (
                    <>
                      <FiUser size={16} />
                      <span className="max-w-[100px] truncate">{user?.name?.split(' ')[0] || 'مستخدم'}</span>
                      <FiChevronDown size={14} className={`transition-transform duration-300 ${isUserMenuOpen ? 'rotate-180' : ''}`} />
                    </>
                  )}
                </button>

                {/* القائمة المنسدلة */}
                {isUserMenuOpen && !isLoggingOut && (
                  <div
                    className="absolute left-0 mt-2 w-48 rounded-xl shadow-lg overflow-hidden z-50"
                    style={{
                      backgroundColor: 'var(--color-bg-card)',
                      border: `1px solid var(--color-border-light)`,
                    }}
                    onClick={(e) => e.stopPropagation()}
                  >
                    <div className="py-2">
                      <div
                        className="px-4 py-3 border-b"
                        style={{ borderColor: 'var(--color-border-light)' }}
                      >
                        <p className="text-sm font-medium" style={{ color: 'var(--color-text-primary)' }}>
                          {user?.name}
                        </p>
                        <p className="text-xs mt-1" style={{ color: 'var(--color-text-muted)' }}>
                          {user?.email}
                        </p>
                      </div>
                      
                      {isAdmin && (
                        <Link
                          to={ROUTES.ADMIN_DASHBOARD}
                          className="flex items-center gap-3 px-4 py-3 text-sm transition-colors duration-200"
                          style={{ color: 'var(--color-text-secondary)' }}
                          onMouseEnter={(e) => {
                            e.currentTarget.style.backgroundColor = 'var(--color-primary-soft)'
                            e.currentTarget.style.color = 'var(--color-primary-dark)'
                          }}
                          onMouseLeave={(e) => {
                            e.currentTarget.style.backgroundColor = 'transparent'
                            e.currentTarget.style.color = 'var(--color-text-secondary)'
                          }}
                          onClick={() => setIsUserMenuOpen(false)}
                        >
                          <FiBarChart2 size={16} />
                          <span>لوحة التحكم</span>
                        </Link>
                      )}
                      
                      <Link
                        to={ROUTES.PROFILE}
                        className="flex items-center gap-3 px-4 py-3 text-sm transition-colors duration-200"
                        style={{ color: 'var(--color-text-secondary)' }}
                        onMouseEnter={(e) => {
                          e.currentTarget.style.backgroundColor = 'var(--color-primary-soft)'
                          e.currentTarget.style.color = 'var(--color-primary-dark)'
                        }}
                        onMouseLeave={(e) => {
                          e.currentTarget.style.backgroundColor = 'transparent'
                          e.currentTarget.style.color = 'var(--color-text-secondary)'
                        }}
                        onClick={() => setIsUserMenuOpen(false)}
                      >
                        <FiUser size={16} />
                        <span>الملف الشخصي</span>
                      </Link>
                      
                      <Link
                        to={ROUTES.ORDERS}
                        className="flex items-center gap-3 px-4 py-3 text-sm transition-colors duration-200"
                        style={{ color: 'var(--color-text-secondary)' }}
                        onMouseEnter={(e) => {
                          e.currentTarget.style.backgroundColor = 'var(--color-primary-soft)'
                          e.currentTarget.style.color = 'var(--color-primary-dark)'
                        }}
                        onMouseLeave={(e) => {
                          e.currentTarget.style.backgroundColor = 'transparent'
                          e.currentTarget.style.color = 'var(--color-text-secondary)'
                        }}
                        onClick={() => setIsUserMenuOpen(false)}
                      >
                        <FiGrid size={16} />
                        <span>طلباتي</span>
                      </Link>
                      
                      <div className="border-t my-1" style={{ borderColor: 'var(--color-border-light)' }} />
                      
                      <button
                        onClick={handleLogout}
                        disabled={isLoggingOut}
                        className="flex items-center gap-3 px-4 py-3 text-sm transition-colors duration-200 w-full text-right disabled:opacity-50"
                        style={{ color: 'var(--color-danger)' }}
                        onMouseEnter={(e) => {
                          e.currentTarget.style.backgroundColor = 'var(--color-danger-bg)'
                        }}
                        onMouseLeave={(e) => {
                          e.currentTarget.style.backgroundColor = 'transparent'
                        }}
                      >
                        {isLoggingOut ? (
                          <>
                            <div className="w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin" />
                            <span>جاري الخروج...</span>
                          </>
                        ) : (
                          <>
                            <FiLogOut size={16} />
                            <span>تسجيل خروج</span>
                          </>
                        )}
                      </button>
                    </div>
                  </div>
                )}
              </div>
            ) : (
              <Link
                to={ROUTES.LOGIN}
                className="px-4 py-2 rounded-lg transition-all duration-300 hover:shadow-sm transform hover:-translate-y-0.5"
                style={{
                  backgroundColor: 'var(--color-primary)',
                  color: 'white',
                }}
                onMouseEnter={(e) => {
                  e.currentTarget.style.backgroundColor = 'var(--color-primary-dark)'
                }}
                onMouseLeave={(e) => {
                  e.currentTarget.style.backgroundColor = 'var(--color-primary)'
                }}
              >
                تسجيل الدخول
              </Link>
            )}

            {/* زر القائمة للجوال */}
            <button
              onClick={() => setIsMenuOpen(!isMenuOpen)}
              className="md:hidden p-2 rounded-lg transition-all duration-300"
              style={{
                backgroundColor: 'transparent',
                color: 'var(--color-text-secondary)',
                border: `1px solid var(--color-border-light)`,
              }}
            >
              {isMenuOpen ? <FiX size={20} /> : <FiMenu size={20} />}
            </button>
          </div>
        </div>

        {/* القائمة للجوال */}
        {isMenuOpen && (
          <div
            className="md:hidden py-3 mt-3 rounded-lg space-y-1"
            style={{
              backgroundColor: 'var(--color-bg-card)',
              border: `1px solid var(--color-border-light)`,
            }}
          >
            {navLinks.map((link) => {
              const isActive = getIsActive(link.path)
              return (
                <Link
                  key={link.path}
                  to={link.path}
                  className={`
                    flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300
                    ${isActive ? 'bg-primary-soft text-primary-dark' : ''}
                  `}
                  style={{
                    backgroundColor: isActive ? 'var(--color-primary-soft)' : 'transparent',
                    color: isActive ? 'var(--color-primary-dark)' : 'var(--color-text-secondary)',
                  }}
                  onClick={() => setIsMenuOpen(false)}
                >
                  <link.icon size={18} />
                  <span>{link.name}</span>
                </Link>
              )
            })}

            <div className="border-t my-2" style={{ borderColor: 'var(--color-border-light)' }} />

            <Link
              to={ROUTES.CART}
              className={`
                flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300
                ${location.pathname === ROUTES.CART ? 'bg-primary-soft text-primary-dark' : ''}
              `}
              style={{
                backgroundColor: location.pathname === ROUTES.CART ? 'var(--color-primary-soft)' : 'transparent',
                color: location.pathname === ROUTES.CART ? 'var(--color-primary-dark)' : 'var(--color-text-secondary)',
              }}
              onClick={() => setIsMenuOpen(false)}
            >
              <FiShoppingCart size={18} />
              <span>السلة</span>
              {itemsCount > 0 && (
                <span
                  className="text-xs rounded-full px-2 py-0.5"
                  style={{
                    backgroundColor: 'var(--color-danger)',
                    color: 'white',
                  }}
                >
                  {itemsCount}
                </span>
              )}
            </Link>

            {isAuthenticated ? (
              <>
                <div className="border-t my-2" style={{ borderColor: 'var(--color-border-light)' }} />
                <div
                  className="px-4 py-3"
                  style={{ borderBottom: `1px solid var(--color-border-light)` }}
                >
                  <p className="text-sm font-medium" style={{ color: 'var(--color-text-primary)' }}>
                    {user?.name}
                  </p>
                  <p className="text-xs mt-1" style={{ color: 'var(--color-text-muted)' }}>
                    {user?.email}
                  </p>
                </div>
                {isAdmin && (
                  <Link
                    to={ROUTES.ADMIN_DASHBOARD}
                    className="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300"
                    style={{ color: 'var(--color-text-secondary)' }}
                    onClick={() => setIsMenuOpen(false)}
                  >
                    <FiBarChart2 size={18} />
                    <span>لوحة التحكم</span>
                  </Link>
                )}
                <Link
                  to={ROUTES.PROFILE}
                  className="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300"
                  style={{ color: 'var(--color-text-secondary)' }}
                  onClick={() => setIsMenuOpen(false)}
                >
                  <FiUser size={18} />
                  <span>الملف الشخصي</span>
                </Link>
                <Link
                  to={ROUTES.ORDERS}
                  className="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300"
                  style={{ color: 'var(--color-text-secondary)' }}
                  onClick={() => setIsMenuOpen(false)}
                >
                  <FiGrid size={18} />
                  <span>طلباتي</span>
                </Link>
                <button
                  onClick={handleLogout}
                  disabled={isLoggingOut}
                  className="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 w-full text-right disabled:opacity-50"
                  style={{ color: 'var(--color-danger)' }}
                >
                  {isLoggingOut ? (
                    <>
                      <div className="w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin" />
                      <span>جاري الخروج...</span>
                    </>
                  ) : (
                    <>
                      <FiLogOut size={18} />
                      <span>تسجيل خروج</span>
                    </>
                  )}
                </button>
              </>
            ) : (
              <>
                <div className="border-t my-2" style={{ borderColor: 'var(--color-border-light)' }} />
                <Link
                  to={ROUTES.LOGIN}
                  className="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300"
                  style={{ color: 'var(--color-primary)' }}
                  onClick={() => setIsMenuOpen(false)}
                >
                  <FiUser size={18} />
                  <span>تسجيل الدخول</span>
                </Link>
              </>
            )}
          </div>
        )}
      </div>
    </nav>
  )
}

export default Navbar