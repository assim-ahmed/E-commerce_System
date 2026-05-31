import { NavLink } from 'react-router-dom'
import { 
  FiGrid, 
  FiShoppingBag, 
  FiTag, 
  FiPackage, 
  FiShoppingCart, 
  FiStar, 
  FiPieChart,
  FiLogOut
} from 'react-icons/fi'
import useAuthStore from '../../../store/authStore'
import { ROUTES } from '../../../utils/constants'

const Sidebar = () => {
  const { logout } = useAuthStore()

  const handleLogout = async () => {
    await logout()
  }

  const menuItems = [
    { name: 'لوحة التحكم', path: ROUTES.ADMIN_DASHBOARD, icon: FiPieChart },
    { name: 'المنتجات', path: ROUTES.ADMIN_PRODUCTS, icon: FiPackage },
    { name: 'التصنيفات', path: ROUTES.ADMIN_CATEGORIES, icon: FiGrid },
    { name: 'البراندات', path: ROUTES.ADMIN_BRANDS, icon: FiTag },
    { name: 'الطلبات', path: ROUTES.ADMIN_ORDERS, icon: FiShoppingCart },
    { name: 'الكوبونات', path: ROUTES.ADMIN_COUPONS, icon: FiShoppingBag },
    { name: 'التقييمات', path: ROUTES.ADMIN_REVIEWS, icon: FiStar },
  ]

  return (
    <aside 
      className="w-64 min-h-screen flex flex-col shadow-lg"
      style={{
        backgroundColor: 'var(--color-bg-card)',
        borderLeft: `1px solid var(--color-border-light)`,
      }}
    >
      {/* عنوان القائمة */}
      <div 
        className="p-6 border-b"
        style={{ borderColor: 'var(--color-border-light)' }}
      >
        <h2 
          className="text-xl font-bold text-center"
          style={{ color: 'var(--color-primary)' }}
        >
          لوحة الإدارة
        </h2>
      </div>

      {/* قائمة الروابط */}
      <nav className="flex-1 py-6">
        <ul className="space-y-2 px-4">
          {menuItems.map((item) => (
            <li key={item.path}>
              <NavLink
                to={item.path}
                end={item.path === ROUTES.ADMIN_DASHBOARD}
                className={({ isActive }) => `
                  flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200
                  ${isActive ? 'bg-primary-soft text-primary-dark' : ''}
                `}
                style={({ isActive }) => ({
                  color: isActive ? 'var(--color-primary-dark)' : 'var(--color-text-secondary)',
                  backgroundColor: isActive ? 'var(--color-primary-soft)' : 'transparent',
                })}
                onMouseEnter={(e) => {
                  if (!e.currentTarget.classList.contains('active')) {
                    e.currentTarget.style.backgroundColor = 'var(--color-primary-soft)'
                    e.currentTarget.style.color = 'var(--color-primary-dark)'
                  }
                }}
                onMouseLeave={(e) => {
                  if (!e.currentTarget.classList.contains('active')) {
                    e.currentTarget.style.backgroundColor = 'transparent'
                    e.currentTarget.style.color = 'var(--color-text-secondary)'
                  }
                }}
              >
                <item.icon size={20} />
                <span>{item.name}</span>
              </NavLink>
            </li>
          ))}
        </ul>
      </nav>

      {/* زر تسجيل الخروج */}
      <div className="p-4 border-t" style={{ borderColor: 'var(--color-border-light)' }}>
        <button
          onClick={handleLogout}
          className="flex items-center gap-3 w-full px-4 py-3 rounded-lg transition-all duration-200"
          style={{
            color: 'var(--color-danger)',
            backgroundColor: 'transparent',
          }}
          onMouseEnter={(e) => {
            e.currentTarget.style.backgroundColor = 'var(--color-danger-bg)'
          }}
          onMouseLeave={(e) => {
            e.currentTarget.style.backgroundColor = 'transparent'
          }}
        >
          <FiLogOut size={20} />
          <span>تسجيل خروج</span>
        </button>
      </div>
    </aside>
  )
}

export default Sidebar