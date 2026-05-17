import { useEffect } from 'react'
import { useParams, useSearchParams, Link } from 'react-router-dom'
import { useShallow } from 'zustand/react/shallow'
import useCategoryStore from '../store/categoryStore'
import ProductList from '../components/products/ProductList'
import Pagination from '../components/common/Pagination'
import { ROUTES } from '../utils/constants'

const CategoryProductsPage = () => {
  const { id } = useParams()
  const [searchParams, setSearchParams] = useSearchParams()
  const currentPageFromUrl = parseInt(searchParams.get('page') || '1', 10)

  const { 
    selectedCategory, 
    categoryProducts, 
    meta, 
    isLoading, 
    error, 
    fetchCategoryById, 
    fetchCategoryProducts,
    reset
  } = useCategoryStore(
    useShallow((state) => ({
      selectedCategory: state.selectedCategory,
      categoryProducts: state.categoryProducts,
      meta: state.meta,
      isLoading: state.isLoading,
      error: state.error,
      fetchCategoryById: state.fetchCategoryById,
      fetchCategoryProducts: state.fetchCategoryProducts,
      reset: state.reset,
    }))
  )

  useEffect(() => {
    if (id) {
      fetchCategoryById(id)
      fetchCategoryProducts(id, currentPageFromUrl, 15)
    }
    
    return () => {
      reset()
    }
  }, [id, currentPageFromUrl])

  const handlePageChange = (page) => {
    setSearchParams({ page: page.toString() })
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  useEffect(() => {
    if (id && !isLoading && selectedCategory) {
      fetchCategoryProducts(id, currentPageFromUrl, 15)
    }
  }, [currentPageFromUrl])

  const currentPage = meta?.current_page || 1
  const lastPage = meta?.last_page || 1

  if (isLoading && !selectedCategory) {
    return (
      <div className="flex justify-center items-center py-20">
        <div 
          className="w-12 h-12 rounded-full animate-spin border-4 border-t-transparent"
          style={{ borderColor: 'var(--color-primary)', borderTopColor: 'transparent' }}
        />
      </div>
    )
  }

  return (
    <div className="container mx-auto px-4 py-8">
      {/* رابط العودة إلى التصنيفات */}
      <Link 
        to={ROUTES.CATEGORIES}
        className="inline-flex items-center gap-2 mb-6 text-sm hover:underline"
        style={{ color: 'var(--color-primary)' }}
      >
        ← العودة إلى التصنيفات
      </Link>

      <h1 
        className="text-3xl font-bold text-center mb-2"
        style={{ color: 'var(--color-text-primary)' }}
      >
        {selectedCategory?.name || 'التصنيف'}
      </h1>
      
      {selectedCategory?.description && (
        <p 
          className="text-center mb-8"
          style={{ color: 'var(--color-text-muted)' }}
        >
          {selectedCategory.description}
        </p>
      )}
      
      <ProductList products={categoryProducts} isLoading={isLoading} error={error} />
      
      {!isLoading && lastPage > 1 && (
        <Pagination 
          currentPage={currentPage}
          lastPage={lastPage}
          onPageChange={handlePageChange}
        />
      )}
    </div>
  )
}

export default CategoryProductsPage