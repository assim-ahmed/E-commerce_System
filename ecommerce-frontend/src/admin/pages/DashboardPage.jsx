import { useEffect, useState } from 'react'
import AdminLayout from '../components/AdminLayout'
import StatCard from '../components/StatCard'
import {
  FiDollarSign,
  FiShoppingCart,
  FiPackage,
  FiUsers,
  FiAlertCircle,
  FiTrendingUp,
} from 'react-icons/fi'
import api from '../../../services/api'
import { API_URL } from '../../../utils/constants'

const DashboardPage = () => {
  const [stats, setStats] = useState({
    total_sales: 0,
    total_orders: 0,
    total_products: 0,
    total_customers: 0,
    low_stock_products: 0,
    today_sales: 0,
    this_month_sales: 0,
  })
  const [recentOrders, setRecentOrders] = useState([])
  const [topProducts, setTopProducts] = useState([])
  const [lowStock, setLowStock] = useState([])
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    fetchDashboardData()
  }, [])

  const fetchDashboardData = async () => {
    setIsLoading(true)
    try {
      // جلب الإحصائيات
      const statsRes = await api.get(`${API_URL}/admin/dashboard/stats`)
      if (statsRes.data.success) {
        setStats(statsRes.data.data)
      }

      // جلب أحدث الطلبات
      const ordersRes = await api.get(`${API_URL}/admin/dashboard/recent-orders?limit=5`)
      if (ordersRes.data.success) {
        setRecentOrders(ordersRes.data.data)
      }

      // جلب أفضل المنتجات
      const productsRes = await api.get(`${API_URL}/admin/dashboard/top-products?limit=5`)
      if (productsRes.data.success) {
        setTopProducts(productsRes.data.data)
      }

      // جلب المنتجات قليلة المخزون
      const lowStockRes = await api.get(`${API_URL}/products/low-stock`)
      if (lowStockRes.data.success) {
        setLowStock(lowStockRes.data.data)
      }
    } catch (error) {
      console.error('Error fetching dashboard data:', error)
    } finally {
      setIsLoading(false)
    }
  }

  const formatPrice = (price) => {
    return Number(price).toLocaleString() + ' ر.س'
  }

  const getOrderStatusColor = (status) => {
    switch (status) {
      case 'completed':
        return 'var(--color-success)'
      case 'cancelled':
        return 'var(--color-danger)'
      case 'pending':
        return 'var(--color-warning)'
      default:
        return 'var(--color-primary)'
    }
  }

  const getOrderStatusText = (status) => {
    switch (status) {
      case 'completed':
        return 'مكتمل'
      case 'cancelled':
        return 'ملغي'
      case 'pending':
        return 'قيد المعالجة'
      case 'processing':
        return 'جاري التجهيز'
      default:
        return status
    }
  }

  if (isLoading) {
    return (
      <AdminLayout title="لوحة التحكم">
        <div className="flex justify-center items-center h-64">
          <div
            className="w-12 h-12 rounded-full animate-spin border-4 border-t-transparent"
            style={{ borderColor: 'var(--color-primary)', borderTopColor: 'transparent' }}
          />
        </div>
      </AdminLayout>
    )
  }

  return (
    <AdminLayout title="لوحة التحكم">
      {/* بطاقات الإحصائيات */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <StatCard
          title="إجمالي المبيعات"
          value={formatPrice(stats.total_sales)}
          icon={FiDollarSign}
          color="primary"
        />
        <StatCard
          title="عدد الطلبات"
          value={stats.total_orders}
          icon={FiShoppingCart}
          color="primary"
        />
        <StatCard
          title="المنتجات"
          value={stats.total_products}
          icon={FiPackage}
          color="primary"
        />
        <StatCard
          title="العملاء"
          value={stats.total_customers}
          icon={FiUsers}
          color="primary"
        />
      </div>

      {/* صف ثاني: مبيعات اليوم وهذا الشهر */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <StatCard
          title="مبيعات اليوم"
          value={formatPrice(stats.today_sales)}
          icon={FiTrendingUp}
          color="success"
        />
        <StatCard
          title="مبيعات هذا الشهر"
          value={formatPrice(stats.this_month_sales)}
          icon={FiTrendingUp}
          color="warning"
        />
      </div>

      {/* أحدث الطلبات */}
      <div className="mb-8">
        <h2 className="text-lg font-semibold mb-4" style={{ color: 'var(--color-text-primary)' }}>
          أحدث الطلبات
        </h2>
        <div
          className="rounded-xl overflow-hidden"
          style={{
            backgroundColor: 'var(--color-bg-card)',
            border: `1px solid var(--color-border-light)`,
          }}
        >
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr style={{ borderBottom: `1px solid var(--color-border-light)` }}>
                  <th className="text-right p-3">رقم الطلب</th>
                  <th className="text-right p-3">العميل</th>
                  <th className="text-right p-3">المبلغ</th>
                  <th className="text-right p-3">الحالة</th>
                  <th className="text-right p-3">التاريخ</th>
                </tr>
              </thead>
              <tbody>
                {recentOrders.map((order) => (
                  <tr
                    key={order.id}
                    style={{ borderBottom: `1px solid var(--color-border-light)` }}
                  >
                    <td className="p-3">{order.order_number}</td>
                    <td className="p-3">{order.customer_name}</td>
                    <td className="p-3">{formatPrice(order.total)}</td>
                    <td className="p-3">
                      <span
                        className="px-2 py-1 rounded-full text-xs"
                        style={{
                          backgroundColor: getOrderStatusColor(order.status),
                          color: 'white',
                        }}
                      >
                        {getOrderStatusText(order.status)}
                      </span>
                    </td>
                    <td className="p-3">
                      {new Date(order.created_at).toLocaleDateString('ar')}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {/* صف مزدوج: أفضل المنتجات + تنبيهات المخزون */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* أفضل المنتجات مبيعاً */}
        <div>
          <h2 className="text-lg font-semibold mb-4" style={{ color: 'var(--color-text-primary)' }}>
            أفضل المنتجات مبيعاً
          </h2>
          <div
            className="rounded-xl p-4"
            style={{
              backgroundColor: 'var(--color-bg-card)',
              border: `1px solid var(--color-border-light)`,
            }}
          >
            {topProducts.map((product, index) => (
              <div
                key={product.id}
                className="flex items-center justify-between py-3"
                style={{ borderBottom: index < topProducts.length - 1 ? `1px solid var(--color-border-light)` : 'none' }}
              >
                <div className="flex items-center gap-3">
                  <span className="font-bold text-lg" style={{ color: 'var(--color-primary)' }}>
                    #{index + 1}
                  </span>
                  <div>
                    <p className="font-medium" style={{ color: 'var(--color-text-primary)' }}>
                      {product.name}
                    </p>
                    <p className="text-sm" style={{ color: 'var(--color-text-muted)' }}>
                      تم بيع {product.total_quantity_sold} قطعة
                    </p>
                  </div>
                </div>
                <span className="font-bold" style={{ color: 'var(--color-primary)' }}>
                  {formatPrice(product.total_revenue)}
                </span>
              </div>
            ))}
          </div>
        </div>

        {/* تنبيهات المخزون المنخفض */}
        <div>
          <h2 className="text-lg font-semibold mb-4" style={{ color: 'var(--color-text-primary)' }}>
            <FiAlertCircle className="inline ml-2" />
            تنبيهات المخزون المنخفض
          </h2>
          <div
            className="rounded-xl p-4"
            style={{
              backgroundColor: 'var(--color-bg-card)',
              border: `1px solid var(--color-border-light)`,
            }}
          >
            {lowStock.length === 0 ? (
              <p className="text-center py-4" style={{ color: 'var(--color-success)' }}>
                جميع المنتجات متوفرة بالمخزون
              </p>
            ) : (
              lowStock.map((product) => (
                <div
                  key={product.id}
                  className="flex items-center justify-between py-3"
                  style={{ borderBottom: `1px solid var(--color-border-light)` }}
                >
                  <div>
                    <p className="font-medium" style={{ color: 'var(--color-text-primary)' }}>
                      {product.name}
                    </p>
                    <p className="text-sm" style={{ color: 'var(--color-text-muted)' }}>
                      SKU: {product.sku}
                    </p>
                  </div>
                  <div className="text-center">
                    <p className="text-sm" style={{ color: 'var(--color-text-muted)' }}>
                      المخزون الحالي
                    </p>
                    <p className="font-bold text-lg" style={{ color: 'var(--color-danger)' }}>
                      {product.stock_quantity}
                    </p>
                  </div>
                </div>
              ))
            )}
          </div>
        </div>
      </div>
    </AdminLayout>
  )
}

export default DashboardPage