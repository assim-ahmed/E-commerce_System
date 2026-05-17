import { useEffect } from 'react'
import { Link } from 'react-router-dom'
import { useShallow } from 'zustand/react/shallow'
import useCategoryStore from '../store/categoryStore'
import { ROUTES } from '../utils/constants'

const CategoriesPage = () => {
  const { categories, isLoading, error, fetchCategories } = useCategoryStore(
    useShallow((state) => ({
      categories: state.categories,
      isLoading: state.isLoading,
      error: state.error,
      fetchCategories: state.fetchCategories,
    }))
  )

  useEffect(() => {
    fetchCategories()
  }, [])

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

  if (!categories || categories.length === 0) {
    return (
      <div className="text-center py-20">
        <p style={{ color: 'var(--color-text-muted)' }}>لا توجد تصنيفات</p>
      </div>
    )
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 
        className="text-3xl font-bold text-center mb-8"
        style={{ color: 'var(--color-text-primary)' }}
      >
        التصنيفات
      </h1>
      
      <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        {categories.map((category) => (
          <Link
            key={category.id}
            to={`${ROUTES.CATEGORIES}/${category.id}`}
            className="block p-6 rounded-xl text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
            style={{
              backgroundColor: 'var(--color-bg-card)',
              border: `1px solid var(--color-border-light)`,
            }}
          >
            <div 
              className="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4"
              style={{ backgroundColor: 'var(--color-primary-soft)' }}
            >
              <span className="text-3xl">
                {category.name === 'Electronics' && '📱'}
                {category.name === 'Clothing' && '👕'}
                {category.name === 'Books' && '📚'}
                {category.name === 'Home & Garden' && '🏠'}
                {category.name === 'Sports' && '⚽'}
                {!['Electronics', 'Clothing', 'Books', 'Home & Garden', 'Sports'].includes(category.name) && '📁'}
              </span>
            </div>
            <h3 
              className="text-lg font-semibold mb-2"
              style={{ color: 'var(--color-text-primary)' }}
            >
              {category.name}
            </h3>
            {category.description && (
              <p 
                className="text-sm line-clamp-2"
                style={{ color: 'var(--color-text-muted)' }}
              >
                {category.description}
              </p>
            )}
          </Link>
        ))}
      </div>
    </div>
  )
}

export default CategoriesPage