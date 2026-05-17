import { BrowserRouter, Routes, Route } from 'react-router-dom'
import Navbar from './components/common/Navbar'
import HomePage from './pages/HomePage'
import ProductsPage from './pages/ProductsPage'
import CategoriesPage from './pages/CategoriesPage'
import CategoryProductsPage from './pages/CategoryProductsPage'
import { ROUTES } from './utils/constants'

function App() {
  return (
    <BrowserRouter>
      <div className="min-h-screen flex flex-col">
        <Navbar />
        <main className="flex-grow">
          <Routes>
            <Route path={ROUTES.HOME} element={<HomePage />} />
            <Route path={ROUTES.PRODUCTS} element={<ProductsPage />} />
            <Route path={ROUTES.FEATURED_PRODUCTS} element={<ProductsPage />} />
            <Route path={ROUTES.CATEGORIES} element={<CategoriesPage />} />
            <Route path={`${ROUTES.CATEGORIES}/:id`} element={<CategoryProductsPage />} />
          </Routes>
        </main>
      </div>
    </BrowserRouter>
  )
}

export default App