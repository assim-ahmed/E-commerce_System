import { useState } from 'react'
import { Link, NavLink } from 'react-router-dom'
import { 
  FiShoppingCart, 
  FiMoon, 
  FiSun,
  FiMenu,
  FiX,
  FiHome,
  FiGrid,
  FiTag
} from 'react-icons/fi'
import useDarkMode from '../../hooks/useDarkMode'

const Navbar = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false)
  const { isDark, toggle } = useDarkMode()

  const navLinks = [
    { name: 'الرئيسية', path: '/', icon: FiHome },
    { name: 'المنتجات', path: '/products', icon: FiGrid },
    { name: 'التصنيفات', path: '/categories', icon: FiTag },
  ]

  return (
    <nav 
      className="sticky top-0 z-50 shadow-md transition-colors duration-300"
      style={{ backgroundColor: 'var(--color-bg-navbar)' }}
    >
      <div className=" mx-auto px-4">
        <div className="flex justify-between items-center h-16">
          
          {/* الشعار */}
          <Link 
            to="/" 
            className="text-2xl font-bold transition-colors duration-300"
            style={{ color: 'var(--color-primary)' }}
          >
            بوينت
          </Link>
          
          {/* الروابط للشاشات الكبيرة */}
          <div className="hidden md:flex items-center gap-3 md:mr-15">
            {navLinks.map((link) => (
              <NavLink
                key={link.path}
                to={link.path}
                className={({ isActive }) => `
                  flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-300
                  ${isActive ? 'active-link' : ''}
                `}
                style={({ isActive }) => ({
                  color: isActive 
                    ? 'var(--color-primary-dark)' 
                    : 'var(--color-text-secondary)',
                  backgroundColor: isActive 
                    ? 'var(--color-primary-soft)' 
                    : 'transparent',
                  border: `1px solid ${isActive 
                    ? 'var(--color-primary)' 
                    : 'var(--color-border-light)'}`
                })}
                onMouseEnter={(e) => {
                  if (!e.currentTarget.classList.contains('active-link')) {
                    e.currentTarget.style.backgroundColor = 'var(--color-primary-soft)'
                    e.currentTarget.style.borderColor = 'var(--color-primary)'
                    e.currentTarget.style.color = 'var(--color-primary-dark)'
                  }
                }}
                onMouseLeave={(e) => {
                  if (!e.currentTarget.classList.contains('active-link')) {
                    e.currentTarget.style.backgroundColor = 'transparent'
                    e.currentTarget.style.borderColor = 'var(--color-border-light)'
                    e.currentTarget.style.color = 'var(--color-text-secondary)'
                  }
                }}
              >
                <link.icon size={18} />
                <span>{link.name}</span>
              </NavLink>
            ))}
          </div>
          
          {/* الأزرار الجانبية */}
          <div className="flex items-center gap-3">
            
            {/* زر السلة */}
            <NavLink
              to="/cart"
              className={({ isActive }) => `
                relative p-2 rounded-lg transition-all duration-300
                ${isActive ? 'active-link' : ''}
              `}
              style={({ isActive }) => ({
                backgroundColor: isActive 
                  ? 'var(--color-primary-soft)' 
                  : 'transparent',
                color: isActive 
                  ? 'var(--color-primary-dark)' 
                  : 'var(--color-text-secondary)',
                border: `1px solid ${isActive 
                  ? 'var(--color-primary)' 
                  : 'var(--color-border-light)'}`
              })}
            >
              <FiShoppingCart size={20} />
              <span 
                className="absolute -top-2 -right-2 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
                style={{ backgroundColor: 'var(--color-danger)' }}
              >
                0
              </span>
            </NavLink>
            
            {/* زر تغيير الوضع */}
            <button
              onClick={toggle}
              className="p-2 rounded-lg transition-all duration-300"
              style={{
                backgroundColor: 'transparent',
                color: 'var(--color-text-secondary)',
                border: `1px solid var(--color-border-light)`
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
              to="/login"
              className="px-4 py-2 rounded-lg transition-all duration-300 hover:shadow-sm transform hover:-translate-y-0.5"
              style={{
                backgroundColor: 'var(--color-primary)',
                color: 'white',
                border: `1px solid var(--color-primary)`
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
                border: `1px solid var(--color-border-light)`
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
              border: `1px solid var(--color-border-light)`
            }}
          >
            {navLinks.map((link) => (
              <NavLink
                key={link.path}
                to={link.path}
                className={({ isActive }) => `
                  flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300
                  ${isActive ? 'active-link' : ''}
                `}
                style={({ isActive }) => ({
                  backgroundColor: isActive 
                    ? 'var(--color-primary-soft)' 
                    : 'transparent',
                  color: isActive 
                    ? 'var(--color-primary-dark)' 
                    : 'var(--color-text-secondary)'
                })}
                onClick={() => setIsMenuOpen(false)}
              >
                <link.icon size={18} />
                <span>{link.name}</span>
              </NavLink>
            ))}
            
            <div className="border-t my-2" style={{ borderColor: 'var(--color-border-light)' }} />
            
            <NavLink
              to="/cart"
              className={({ isActive }) => `
                flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300
                ${isActive ? 'active-link' : ''}
              `}
              style={({ isActive }) => ({
                backgroundColor: isActive 
                  ? 'var(--color-primary-soft)' 
                  : 'transparent',
                color: isActive 
                  ? 'var(--color-primary-dark)' 
                  : 'var(--color-text-secondary)'
              })}
              onClick={() => setIsMenuOpen(false)}
            >
              <FiShoppingCart size={18} />
              <span>السلة</span>
            </NavLink>
          </div>
        )}
      </div>
    </nav>
  )
}

export default Navbar