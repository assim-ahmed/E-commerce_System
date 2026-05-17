import { Link } from 'react-router-dom'
import { FiShoppingBag, FiTruck, FiShield, FiHeadphones } from 'react-icons/fi'
import { ROUTES } from '../utils/constants'

const HomePage = () => {
  const features = [
    {
      icon: FiShoppingBag,
      title: 'تسوق سهل',
      description: 'تجربة تسوق سلسة ومريحة',
    },
    {
      icon: FiTruck,
      title: 'توصيل سريع',
      description: 'توصيل خلال 3 أيام',
    },
    {
      icon: FiShield,
      title: 'جودة عالية',
      description: 'منتجات مضمونة',
    },
    {
      icon: FiHeadphones,
      title: 'دعم 24/7',
      description: 'فريق دعم متواصل',
    },
  ]

  return (
    <div>
      {/* القسم الرئيسي */}
      <section 
        className="py-20 text-center"
        style={{ backgroundColor: 'var(--color-primary-soft)' }}
      >
        <div className="container mx-auto px-4">
          <h1 
            className="text-4xl md:text-5xl font-bold mb-4"
            style={{ color: 'var(--color-primary-dark)' }}
          >
            مرحبا بك في بوينت
          </h1>
          <p 
            className="text-lg mb-8 max-w-2xl mx-auto"
            style={{ color: 'var(--color-text-secondary)' }}
          >
            اكتشف أحدث المنتجات بأفضل الأسعار. تسوق الآن واستمتع بتجربة فريدة
          </p>
          <Link
            to={ROUTES.PRODUCTS}
            className="inline-block px-8 py-3 rounded-lg text-white font-semibold transition-all duration-300 hover:shadow-lg hover:-translate-y-1"
            style={{ backgroundColor: 'var(--color-primary)' }}
            onMouseEnter={(e) => {
              e.currentTarget.style.backgroundColor = 'var(--color-primary-dark)'
            }}
            onMouseLeave={(e) => {
              e.currentTarget.style.backgroundColor = 'var(--color-primary)'
            }}
          >
            تسوق الآن
          </Link>
        </div>
      </section>

      {/* قسم المميزات */}
      <section className="py-16 container mx-auto px-4">
        <h2 
          className="text-2xl md:text-3xl font-bold text-center mb-12"
          style={{ color: 'var(--color-text-primary)' }}
        >
          لماذا تختار بوينت؟
        </h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {features.map((feature, index) => (
            <div
              key={index}
              className="text-center p-6 rounded-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
              style={{
                backgroundColor: 'var(--color-bg-card)',
                border: `1px solid var(--color-border-light)`,
              }}
            >
              <feature.icon 
                size={48} 
                className="mx-auto mb-4"
                style={{ color: 'var(--color-primary)' }}
              />
              <h3 
                className="text-xl font-semibold mb-2"
                style={{ color: 'var(--color-text-primary)' }}
              >
                {feature.title}
              </h3>
              <p style={{ color: 'var(--color-text-muted)' }}>
                {feature.description}
              </p>
            </div>
          ))}
        </div>
      </section>

      {/* قسم المنتجات المميزة (سيضاف لاحقاً) */}
    </div>
  )
}

export default HomePage