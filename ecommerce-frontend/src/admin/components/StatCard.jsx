const StatCard = ({ title, value, icon: Icon, color = 'primary' }) => {
  const getColorStyles = () => {
    switch (color) {
      case 'success':
        return { bg: 'var(--color-success-bg)', text: 'var(--color-success)' }
      case 'danger':
        return { bg: 'var(--color-danger-bg)', text: 'var(--color-danger)' }
      case 'warning':
        return { bg: 'var(--color-warning-bg)', text: 'var(--color-warning)' }
      default:
        return { bg: 'var(--color-primary-soft)', text: 'var(--color-primary-dark)' }
    }
  }

  const styles = getColorStyles()

  return (
    <div
      className="rounded-xl p-6 transition-all duration-300 hover:shadow-lg"
      style={{
        backgroundColor: 'var(--color-bg-card)',
        border: `1px solid var(--color-border-light)`,
      }}
    >
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm mb-1" style={{ color: 'var(--color-text-muted)' }}>
            {title}
          </p>
          <p className="text-2xl font-bold" style={{ color: 'var(--color-text-primary)' }}>
            {value}
          </p>
        </div>
        <div
          className="p-3 rounded-full"
          style={{ backgroundColor: styles.bg, color: styles.text }}
        >
          <Icon size={24} />
        </div>
      </div>
    </div>
  )
}

export default StatCard