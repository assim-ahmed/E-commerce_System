import { useState } from 'react'
import { FiTrash2, FiMinus, FiPlus } from 'react-icons/fi'
import { getImageUrl, getPlaceholderImage } from '../../utils/helpers'
import { Link } from 'react-router-dom'
import { ROUTES } from '../../utils/constants'

const CartItem = ({ item, onUpdateQuantity, onRemove }) => {
  const [quantity, setQuantity] = useState(item.quantity)
  const [isUpdating, setIsUpdating] = useState(false)

  const product = item.product
  const imageUrl = getImageUrl(product?.images)

  const handleIncrease = async () => {
    const newQuantity = quantity + 1
    setQuantity(newQuantity)
    setIsUpdating(true)
    await onUpdateQuantity(item.id, newQuantity)
    setIsUpdating(false)
  }

  const handleDecrease = async () => {
    if (quantity > 1) {
      const newQuantity = quantity - 1
      setQuantity(newQuantity)
      setIsUpdating(true)
      await onUpdateQuantity(item.id, newQuantity)
      setIsUpdating(false)
    }
  }

  const handleRemove = async () => {
    setIsUpdating(true)
    await onRemove(item.id)
    setIsUpdating(false)
  }

  return (
    <div
      className="flex flex-col sm:flex-row gap-4 p-4 rounded-xl transition-all duration-300"
      style={{
        backgroundColor: 'var(--color-bg-card)',
        border: `1px solid var(--color-border-light)`,
      }}
    >
      {/* صورة المنتج */}
      <Link to={ROUTES.PRODUCT_DETAILS.replace(':id', product?.id)} className="sm:w-24 h-24 flex-shrink-0">
        <img
          src={imageUrl || getPlaceholderImage(product?.name)}
          alt={product?.name}
          className="w-full h-full object-cover rounded-lg"
          onError={(e) => {
            e.target.src = getPlaceholderImage(product?.name)
          }}
        />
      </Link>

      {/* معلومات المنتج */}
      <div className="flex-grow">
        <Link to={ROUTES.PRODUCT_DETAILS.replace(':id', product?.id)}>
          <h3
            className="font-semibold text-lg mb-1 hover:text-primary transition-colors"
            style={{ color: 'var(--color-text-primary)' }}
          >
            {product?.name}
          </h3>
        </Link>
        
        <div className="flex flex-wrap items-center gap-4 mt-2">
          {/* السعر */}
          <div>
            <span className="text-sm" style={{ color: 'var(--color-text-muted)' }}>السعر:</span>
            <span className="font-semibold mr-1" style={{ color: 'var(--color-primary)' }}>
              {item.price_at_time} ر.س
            </span>
            {item.current_price !== item.price_at_time && (
              <span className="text-xs mr-1" style={{ color: 'var(--color-danger)' }}>
                (السعر الحالي: {item.current_price} ر.س)
              </span>
            )}
          </div>

          {/* المجموع الجزئي */}
          <div>
            <span className="text-sm" style={{ color: 'var(--color-text-muted)' }}>المجموع:</span>
            <span className="font-semibold mr-1" style={{ color: 'var(--color-primary)' }}>
              {item.line_total} ر.س
            </span>
          </div>
        </div>
      </div>

      {/* أزرار التحكم */}
      <div className="flex items-center gap-3 sm:flex-col sm:justify-center">
        <div className="flex items-center gap-2">
          <button
            onClick={handleDecrease}
            disabled={isUpdating || quantity <= 1}
            className="p-1 rounded-lg transition-all duration-300 disabled:opacity-40"
            style={{
              backgroundColor: 'var(--color-primary-soft)',
              color: 'var(--color-primary-dark)',
            }}
          >
            <FiMinus size={16} />
          </button>
          
          <span className="w-8 text-center font-medium">{quantity}</span>
          
          <button
            onClick={handleIncrease}
            disabled={isUpdating}
            className="p-1 rounded-lg transition-all duration-300 disabled:opacity-40"
            style={{
              backgroundColor: 'var(--color-primary-soft)',
              color: 'var(--color-primary-dark)',
            }}
          >
            <FiPlus size={16} />
          </button>
        </div>

        <button
          onClick={handleRemove}
          disabled={isUpdating}
          className="p-2 rounded-lg transition-all duration-300"
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
          <FiTrash2 size={16} />
        </button>
      </div>
    </div>
  )
}

export default CartItem