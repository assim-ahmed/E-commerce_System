import { Link } from 'react-router-dom'
import { FiShoppingCart, FiStar } from 'react-icons/fi'
import { getImageUrl, getPlaceholderImage } from '../../utils/helpers'
import { ROUTES } from '../../utils/constants'

const ProductCard = ({ product }) => {
  const imageUrl = getImageUrl(product.images)

  return (
    <div 
      className="rounded-xl overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1"
      style={{
        backgroundColor: 'var(--color-bg-card)',
        border: `1px solid var(--color-border-light)`,
      }}
    >
      {/* صورة المنتج */}
      <Link to={ROUTES.PRODUCT_DETAILS.replace(':id', product.id)}>
        <div className="h-48 overflow-hidden bg-gray-100">
          {imageUrl ? (
            <img 
              src={imageUrl} 
              alt={product.name}
              className="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
              onError={(e) => {
                e.target.src = getPlaceholderImage(product.name)
              }}
            />
          ) : (
            <div 
              className="w-full h-full flex items-center justify-center"
              style={{ backgroundColor: 'var(--color-primary-soft)' }}
            >
              <img 
                src={getPlaceholderImage(product.name)}
                alt={product.name}
                className="w-full h-full object-cover"
              />
            </div>
          )}
        </div>
      </Link>

      {/* معلومات المنتج */}
      <div className="p-4">
        {/* القسم و الماركة */}
        <div className="flex items-center gap-2 mb-2">
          <span 
            className="text-xs px-2 py-1 rounded-full"
            style={{
              backgroundColor: 'var(--color-primary-soft)',
              color: 'var(--color-primary-dark)',
            }}
          >
            {product.category?.name}
          </span>
          <span className="text-xs" style={{ color: 'var(--color-text-muted)' }}>
            {product.brand?.name}
          </span>
        </div>

        {/* اسم المنتج */}
        <Link to={ROUTES.PRODUCT_DETAILS.replace(':id', product.id)}>
          <h3 
            className="font-semibold text-lg mb-2 line-clamp-1 hover:text-primary transition-colors"
            style={{ color: 'var(--color-text-primary)' }}
          >
            {product.name}
          </h3>
        </Link>

        {/* التقييم */}
        <div className="flex items-center gap-1 mb-3">
          <FiStar className="fill-current" style={{ color: 'var(--color-warning)' }} />
          <span className="text-sm" style={{ color: 'var(--color-text-muted)' }}>
            {product.average_rating || 0}
          </span>
        </div>

        {/* السعر و الزر */}
        <div className="flex items-center justify-between">
          <div>
            <span className="text-xl font-bold" style={{ color: 'var(--color-primary)' }}>
              {product.base_price} ر.س
            </span>
            {product.compare_price && (
              <span 
                className="text-sm line-through mr-2"
                style={{ color: 'var(--color-text-muted)' }}
              >
                {product.compare_price} ر.س
              </span>
            )}
          </div>
          
          <button
            className="p-2 rounded-lg transition-all duration-300 hover:scale-105"
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
            <FiShoppingCart size={18} />
          </button>
        </div>
      </div>
    </div>
  )
}

export default ProductCard