import ProductCard from './ProductCard'

const ProductList = ({ products, isLoading, error }) => {
  if (isLoading) {
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
      </div>
    )
  }

  if (!products || products.length === 0) {
    return (
      <div className="text-center py-20">
        <p style={{ color: 'var(--color-text-muted)' }}>لا توجد منتجات</p>
      </div>
    )
  }

  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      {products.map((product) => (
        <ProductCard key={product.id} product={product} />
      ))}
    </div>
  )
}

export default ProductList