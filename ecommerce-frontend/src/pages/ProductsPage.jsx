import { useEffect } from 'react'
import { useSearchParams, useLocation } from 'react-router-dom'
import { useShallow } from 'zustand/react/shallow'
import useProductStore from '../store/productStore'
import ProductList from '../components/products/ProductList'
import Pagination from '../components/common/Pagination'
import { ROUTES } from '../utils/constants'

const ProductsPage = () => {
  const [searchParams, setSearchParams] = useSearchParams()
  const location = useLocation()
  const currentPageFromUrl = parseInt(searchParams.get('page') || '1', 10)

  const { products, meta, isLoading, error, fetchProducts, fetchFeaturedProducts, changePage, isFeaturedMode } = useProductStore(
    useShallow((state) => ({
      products: state.products,
      meta: state.meta,
      isLoading: state.isLoading,
      error: state.error,
      fetchProducts: state.fetchProducts,
      fetchFeaturedProducts: state.fetchFeaturedProducts,
      changePage: state.changePage,
      isFeaturedMode: state.isFeaturedMode,
    }))
  )

  const isFeaturedRoute = location.pathname === ROUTES.FEATURED_PRODUCTS

  useEffect(() => {
    if (isFeaturedRoute) {
      fetchFeaturedProducts()
    } else {
      fetchProducts(currentPageFromUrl, 15)
    }
  }, [currentPageFromUrl, isFeaturedRoute])

  const handlePageChange = (page) => {
    if (!isFeaturedRoute) {
      changePage(page)
      setSearchParams({ page: page.toString() })
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  }

  const pageTitle = isFeaturedRoute ? 'المنتجات المميزة' : 'جميع المنتجات'
  const currentPage = meta?.current_page || 1
  const lastPage = meta?.last_page || 1

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 
        className="text-3xl font-bold text-center mb-8"
        style={{ color: 'var(--color-text-primary)' }}
      >
        {pageTitle}
      </h1>
      
      <ProductList products={products} isLoading={isLoading} error={error} />
      
      {!isLoading && !isFeaturedRoute && (
        <Pagination 
          currentPage={currentPage}
          lastPage={lastPage}
          onPageChange={handlePageChange}
        />
      )}
    </div>
  )
}

export default ProductsPage