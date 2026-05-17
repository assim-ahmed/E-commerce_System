import { useEffect } from 'react'
import { Link } from 'react-router-dom'
import { useShallow } from 'zustand/react/shallow'
import useCartStore from '../store/cartStore'
import CartItem from '../components/cart/CartItem'
import { ROUTES } from '../utils/constants'

const CartPage = () => {
  const {
    items,
    subtotal,
    total,
    discountAmount,
    couponCode,
    itemsCount,
    isLoading,
    error,
    fetchCart,
    updateQuantity,
    removeFromCart,
    clearCart,
  } = useCartStore(
    useShallow((state) => ({
      items: state.items,
      subtotal: state.subtotal,
      total: state.total,
      discountAmount: state.discountAmount,
      couponCode: state.couponCode,
      itemsCount: state.itemsCount,
      isLoading: state.isLoading,
      error: state.error,
      fetchCart: state.fetchCart,
      updateQuantity: state.updateQuantity,
      removeFromCart: state.removeFromCart,
      clearCart: state.clearCart,
    }))
  )

  useEffect(() => {
    fetchCart()
  }, [])

  const handleUpdateQuantity = async (itemId, quantity) => {
    await updateQuantity(itemId, quantity)
  }

  const handleRemoveItem = async (itemId) => {
    await removeFromCart(itemId)
  }

  const handleClearCart = async () => {
    if (window.confirm('هل أنت متأكد من تفريغ السلة؟')) {
      await clearCart()
    }
  }

  if (isLoading && items.length === 0) {
    return (
      <div className="flex justify-center items-center py-20">
        <div
          className="w-12 h-12 rounded-full animate-spin border-4 border-t-transparent"
          style={{ borderColor: 'var(--color-primary)', borderTopColor: 'transparent' }}
        />
      </div>
    )
  }

  if (error) {
    return (
      <div className="text-center py-20">
        <p style={{ color: 'var(--color-danger)' }}>{error}</p>
        <button
          onClick={() => fetchCart()}
          className="mt-4 px-4 py-2 rounded-lg"
          style={{
            backgroundColor: 'var(--color-primary)',
            color: 'white',
          }}
        >
          إعادة المحاولة
        </button>
      </div>
    )
  }

  if (items.length === 0) {
    return (
      <div className="container mx-auto px-4 py-8 text-center">
        <div
          className="max-w-md mx-auto p-8 rounded-xl"
          style={{
            backgroundColor: 'var(--color-bg-card)',
            border: `1px solid var(--color-border-light)`,
          }}
        >
          <h2 className="text-2xl font-bold mb-4" style={{ color: 'var(--color-text-primary)' }}>
            سلتك فارغة
          </h2>
          <p className="mb-6" style={{ color: 'var(--color-text-muted)' }}>
            لم تقم بإضافة أي منتجات إلى سلة التسوق بعد
          </p>
          <Link
            to={ROUTES.PRODUCTS}
            className="inline-block px-6 py-3 rounded-lg transition-all duration-300"
            style={{
              backgroundColor: 'var(--color-primary)',
              color: 'white',
            }}
          >
            تسوق الآن
          </Link>
        </div>
      </div>
    )
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-3xl font-bold text-center mb-8" style={{ color: 'var(--color-text-primary)' }}>
        سلة التسوق
      </h1>

      <div className="flex flex-col lg:flex-row gap-8">
        {/* قائمة المنتجات */}
        <div className="flex-grow">
          <div className="space-y-4">
            {items.map((item) => (
              <CartItem
                key={item.id}
                item={item}
                onUpdateQuantity={handleUpdateQuantity}
                onRemove={handleRemoveItem}
              />
            ))}
          </div>

          <button
            onClick={handleClearCart}
            className="mt-6 px-4 py-2 rounded-lg transition-all duration-300"
            style={{
              backgroundColor: 'transparent',
              color: 'var(--color-danger)',
              border: `1px solid var(--color-border-light)`,
            }}
            onMouseEnter={(e) => {
              e.currentTarget.style.backgroundColor = 'var(--color-danger)'
              e.currentTarget.style.color = 'white'
            }}
            onMouseLeave={(e) => {
              e.currentTarget.style.backgroundColor = 'transparent'
              e.currentTarget.style.color = 'var(--color-danger)'
            }}
          >
            تفريغ السلة
          </button>
        </div>

        {/* ملخص الطلب */}
        <div className="lg:w-96">
          <div
            className="p-6 rounded-xl sticky top-24"
            style={{
              backgroundColor: 'var(--color-bg-card)',
              border: `1px solid var(--color-border-light)`,
            }}
          >
            <h2 className="text-xl font-bold mb-4" style={{ color: 'var(--color-text-primary)' }}>
              ملخص الطلب
            </h2>

            <div className="space-y-3 mb-4">
              <div className="flex justify-between">
                <span style={{ color: 'var(--color-text-muted)' }}>عدد المنتجات</span>
                <span style={{ color: 'var(--color-text-primary)' }}>{itemsCount}</span>
              </div>
              <div className="flex justify-between">
                <span style={{ color: 'var(--color-text-muted)' }}>المجموع الفرعي</span>
                <span style={{ color: 'var(--color-text-primary)' }}>{subtotal} ر.س</span>
              </div>
              
              {discountAmount > 0 && (
                <div className="flex justify-between">
                  <span style={{ color: 'var(--color-success)' }}>الخصم</span>
                  <span style={{ color: 'var(--color-success)' }}>- {discountAmount} ر.س</span>
                </div>
              )}
              
              {couponCode && (
                <div className="flex justify-between">
                  <span style={{ color: 'var(--color-primary)' }}>كود الخصم</span>
                  <span style={{ color: 'var(--color-primary)' }}>{couponCode}</span>
                </div>
              )}
            </div>

            <div className="border-t pt-3 mb-6" style={{ borderColor: 'var(--color-border-light)' }}>
              <div className="flex justify-between font-bold text-lg">
                <span>الإجمالي</span>
                <span style={{ color: 'var(--color-primary)' }}>{total} ر.س</span>
              </div>
            </div>

            <Link
              to={ROUTES.CHECKOUT}
              className="block text-center w-full py-3 rounded-lg transition-all duration-300"
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
              إتمام الشراء
            </Link>
          </div>
        </div>
      </div>
    </div>
  )
}

export default CartPage