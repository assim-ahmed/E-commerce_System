const Pagination = ({ currentPage, lastPage, onPageChange }) => {
  const handlePrevious = () => {
    if (currentPage > 1) {
      onPageChange(currentPage - 1)
    }
  }

  const handleNext = () => {
    if (currentPage < lastPage) {
      onPageChange(currentPage + 1)
    }
  }

  if (lastPage <= 1) {
    return null
  }

  return (
    <div className="flex justify-center items-center gap-3 mt-8">
      <button
        onClick={handlePrevious}
        disabled={currentPage === 1}
        className="px-4 py-2 rounded-lg transition-all duration-300 disabled:opacity-40 disabled:cursor-not-allowed"
        style={{
          backgroundColor: 'transparent',
          color: 'var(--color-text-secondary)',
          border: `1px solid var(--color-border-light)`,
        }}
        onMouseEnter={(e) => {
          if (currentPage !== 1) {
            e.currentTarget.style.backgroundColor = 'var(--color-primary-soft)'
            e.currentTarget.style.borderColor = 'var(--color-primary)'
            e.currentTarget.style.color = 'var(--color-primary-dark)'
          }
        }}
        onMouseLeave={(e) => {
          if (currentPage !== 1) {
            e.currentTarget.style.backgroundColor = 'transparent'
            e.currentTarget.style.borderColor = 'var(--color-border-light)'
            e.currentTarget.style.color = 'var(--color-text-secondary)'
          }
        }}
      >
        السابق
      </button>

      <span
        className="w-10 h-10 flex items-center justify-center rounded-lg font-medium"
        style={{
          backgroundColor: 'var(--color-primary)',
          color: 'white',
        }}
      >
        {currentPage}
      </span>

      <button
        onClick={handleNext}
        disabled={currentPage === lastPage}
        className="px-4 py-2 rounded-lg transition-all duration-300 disabled:opacity-40 disabled:cursor-not-allowed"
        style={{
          backgroundColor: 'transparent',
          color: 'var(--color-text-secondary)',
          border: `1px solid var(--color-border-light)`,
        }}
        onMouseEnter={(e) => {
          if (currentPage !== lastPage) {
            e.currentTarget.style.backgroundColor = 'var(--color-primary-soft)'
            e.currentTarget.style.borderColor = 'var(--color-primary)'
            e.currentTarget.style.color = 'var(--color-primary-dark)'
          }
        }}
        onMouseLeave={(e) => {
          if (currentPage !== lastPage) {
            e.currentTarget.style.backgroundColor = 'transparent'
            e.currentTarget.style.borderColor = 'var(--color-border-light)'
            e.currentTarget.style.color = 'var(--color-text-secondary)'
          }
        }}
      >
        التالي
      </button>
    </div>
  )
}

export default Pagination