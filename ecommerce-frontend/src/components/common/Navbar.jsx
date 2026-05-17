import { useState } from 'react'
import { Link, useLocation } from 'react-router-dom'
import { 
  FiShoppingCart, 
  FiMoon, 
  FiSun,
  FiMenu,
  FiX,
  FiHome,
  FiGrid,
  FiTag,
  FiStar
} from 'react-icons/fi'
import useDarkMode from '../../hooks/useDarkMode'
import { ROUTES } from '../../utils/constants'

const Navbar = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false)
  const { isDark, toggle } = useDarkMode()
  const location = useLocation()

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
              <span 
                className="absolute -top-2 -right-2 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
                style={{ backgroundColor: 'var(--color-danger)' }}
              >
                0
              </span>
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
            
            {/* زر تسجيل الدخول */}
            <Link
              to={ROUTES.LOGIN}
              className="px-4 py-2 rounded-lg transition-all duration-300 hover:shadow-sm transform hover:-translate-y-0.5"
              style={{
                backgroundColor: 'var(--color-primary)',
                color: 'white',
                border: `1px solid var(--color-primary)`,
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
            </Link>
          </div>
        )}
      </div>
    </nav>
  )
}

export default Navbar