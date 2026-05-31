# 📚 E-Commerce Platform - Complete Documentation

## المشروع المتكامل | Full Stack E-Commerce Solution

---

## 📋 Table of Contents

1. [Project Overview](#project-overview)
2. [Technologies Used](#technologies-used)
3. [Backend Architecture](#backend-architecture)
4. [Frontend Architecture](#frontend-architecture)
5. [Database Schema](#database-schema)
6. [API Documentation](#api-documentation)
7. [Installation & Setup](#installation--setup)
8. [Project Status](#project-status)
9. [Future Roadmap](#future-roadmap)

---

## 🎯 Project Overview

A professional, full-stack e-commerce platform featuring a **RESTful API** built with Laravel and a modern **React frontend**. The system supports single-vendor operations with comprehensive product management, shopping cart, order processing, coupon system, reviews, and admin dashboard.

### Key Features

| Feature | Description |
|---------|-------------|
| **Product Management** | Products with categories, brands, images, and specifications |
| **Product Variants** | Support for colors, sizes, capacities with different pricing |
| **Shopping Cart** | Guest cart with cookie support + user cart persistence |
| **Orders System** | Order creation, status tracking, and snapshots for price protection |
| **Coupons** | Percentage or fixed amount discounts |
| **Reviews** | Post-purchase reviews with admin approval |
| **Inventory Tracking** | Stock management with low stock alerts and movement logs |
| **Notifications** | User notifications for orders, promotions, and low stock |
| **Admin Dashboard** | Complete control panel with statistics and reports |

### User Roles

| User Type | Description |
|-----------|-------------|
| **Admin** | Store owner with full management capabilities |
| **Customer** | Regular users who browse and purchase products |

---

## 🛠️ Technologies Used

### Backend Stack

| Component | Technology |
|-----------|------------|
| **Framework** | Laravel 11/12 |
| **PHP Version** | 8.2+ |
| **Database** | MySQL 8.0+ |
| **Authentication** | Laravel Sanctum |
| **API Style** | RESTful |
| **Architecture** | Service-Repository Pattern |
| **CORS** | Custom middleware with credentials support |

### Frontend Stack

| Component | Technology |
|-----------|------------|
| **Framework** | React 19 |
| **Build Tool** | Vite |
| **Styling** | Tailwind CSS v4 |
| **State Management** | Zustand with persist middleware |
| **Routing** | React Router DOM v7 |
| **HTTP Client** | Axios with interceptors |
| **Icons** | React Icons (Fi icons) |

---

## 🏗️ Backend Architecture

### Service-Repository Pattern

The backend follows a clean **Service-Repository Pattern** that separates business logic from data access, making the code maintainable, testable, and scalable.


┌─────────────────────────────────────────────────────────────────┐
│ CONTROLLER │
│ (Handles HTTP requests/responses) │
└─────────────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────────┐
│ SERVICE INTERFACE │
│ (Defines business logic contracts) │
└─────────────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────────┐
│ SERVICE IMPLEMENTATION │
│ (Contains business logic & caching) │
└─────────────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────────┐
│ REPOSITORY INTERFACE │
│ (Defines data access contracts) │
└─────────────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────────┐
│ REPOSITORY IMPLEMENTATION │
│ (Handles database operations) │
└─────────────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────────┐
│ MODEL │
│ (Eloquent ORM - Database) │
└─────────────────────────────────────────────────────────────────┘



### Directory Structure

app/
├── Contracts/
│ ├── Repositories/ # Repository interfaces
│ │ ├── ProductRepositoryInterface.php
│ │ ├── CategoryRepositoryInterface.php
│ │ └── BrandRepositoryInterface.php
│ └── Services/ # Service interfaces
│ ├── ProductServiceInterface.php
│ ├── AuthServiceInterface.php
│ └── DashboardServiceInterface.php
│
├── Repositories/ # Repository implementations
│ ├── ProductRepository.php
│ ├── CategoryRepository.php
│ └── BrandRepository.php
│
├── Services/ # Service implementations
│ ├── ProductService.php
│ ├── AuthService.php
│ └── DashboardService.php
│
├── Http/
│ ├── Controllers/Api/ # API controllers
│ │ ├── ProductController.php
│ │ ├── AuthController.php
│ │ ├── CartController.php
│ │ ├── OrderController.php
│ │ └── DashboardController.php
│ ├── Requests/ # Form requests for validation
│ │ ├── Product/
│ │ │ └── ProductRequest.php
│ │ └── Auth/
│ │ ├── LoginRequest.php
│ │ └── RegisterRequest.php
│ └── Resources/ # API resources (transformers)
│ ├── ProductResource.php
│ └── CategoryResource.php
│
├── Models/ # Eloquent models (15 models)
│ ├── User.php
│ ├── Product.php
│ ├── Category.php
│ ├── Brand.php
│ ├── Cart.php
│ ├── Order.php
│ └── ... (15 total)
│
├── Http/Middleware/
│ ├── AdminMiddleware.php # Admin role protection
│ └── CorsMiddleware.php # CORS configuration
│
├── database/
│ ├── migrations/ # 15 migration files
│ └── seeders/ # Database seeders
│
└── routes/
└── api.php # All API route definitions



### Why Service-Repository Pattern?

| Benefit | Description |
|---------|-------------|
| **Separation of Concerns** | Business logic separated from data access |
| **Testability** | Easy to mock repositories for unit testing |
| **Maintainability** | Changes to database logic don't affect business rules |
| **Reusability** | Services can be reused across controllers |
| **Scalability** | Easy to add caching, logging, or events |

---

## 🎨 Frontend Architecture

### Service-Store Pattern

The frontend follows a pattern similar to the backend, separating API calls (services) from state management (stores).


┌─────────────────────────────────────────────────────────────────┐
│ COMPONENT │
│ (UI - React Components) │
└─────────────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────────┐
│ STORE │
│ (State Management using Zustand) │
│ - Holds application state (products, cart, auth) │
│ - Contains actions that call services │
│ - Persists data to localStorage │
└─────────────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────────┐
│ SERVICE │
│ (API Communication) │
│ - Makes HTTP requests to backend │
│ - Handles request/response formatting │
│ - Returns standardized responses │
└─────────────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────────┐
│ API CLIENT │
│ (Axios Instance) │
│ - Base URL configuration │
│ - Auth token interceptor │
│ - Credentials (withCredentials) for cookies │
└─────────────────────────────────────────────────────────────────┘



### Frontend Directory Structure

src/
├── components/
│ ├── common/ # Reusable components
│ │ ├── Navbar.jsx # Navigation with auth state
│ │ ├── ProtectedPage.jsx # Route protection for admin
│ │ ├── GuestPage.jsx # Route protection for guests
│ │ └── Pagination.jsx # Pagination component
│ ├── products/ # Product-related components
│ │ ├── ProductCard.jsx # Product card with add to cart
│ │ └── ProductList.jsx # Grid of product cards
│ └── auth/ # Authentication components
│ ├── LoginForm.jsx # Login form
│ └── RegisterForm.jsx # Registration form
│
├── pages/ # Page components
│ ├── HomePage.jsx # Landing page
│ ├── ProductsPage.jsx # Products listing with pagination
│ ├── ProductDetailsPage.jsx # Single product view
│ ├── CategoriesPage.jsx # Categories listing
│ ├── CategoryProductsPage.jsx # Products by category
│ ├── CartPage.jsx # Shopping cart
│ └── AuthPage.jsx # Login/Register page (toggle mode)
│
├── admin/ # Admin section (protected)
│ ├── components/
│ │ ├── AdminLayout.jsx # Layout with sidebar
│ │ ├── Sidebar.jsx # Admin navigation sidebar
│ │ └── StatCard.jsx # Statistics card
│ └── pages/
│ └── DashboardPage.jsx # Admin dashboard
│
├── services/ # API communication layer
│ ├── api.js # Axios instance configuration
│ ├── authService.js # Auth API calls
│ ├── productService.js # Product API calls
│ ├── cartService.js # Cart API calls
│ └── categoryService.js # Category API calls
│
├── store/ # Zustand state management
│ ├── authStore.js # Auth state (user, token)
│ ├── cartStore.js # Cart state (items, totals)
│ └── productStore.js # Product state (products, pagination)
│
├── hooks/ # Custom React hooks
│ └── useDarkMode.js # Dark mode toggle
│
├── utils/ # Utility functions
│ └── constants.js # App constants (routes, API URLs)
│
└── App.jsx # Main app with routes


### Why Service-Store Pattern?

| Benefit | Description |
|---------|-------------|
| **Separation of Concerns** | API logic separated from state management |
| **Reusability** | Services can be used by multiple stores/components |
| **Testability** | Easy to mock services for component testing |
| **Maintainability** | API changes only affect service files |
| **Persistence** | Zustand persist middleware handles localStorage |

---

## 💾 Database Schema

### Overview

| Element | Count |
|---------|-------|
| **Tables** | 15 |
| **Foreign Keys** | 17 |
| **Supported Products** | 100,000+ |
| **Storage Engine** | MySQL |

### Tables List

| # | Table | Purpose |
|---|-------|---------|
| 1 | `users` | User accounts (admin/customer) |
| 2 | `addresses` | User shipping/billing addresses |
| 3 | `categories` | Product categories |
| 4 | `brands` | Product brands |
| 5 | `products` | Main products table |
| 6 | `product_variants` | Product variations (color, size, capacity) |
| 7 | `coupons` | Discount coupons |
| 8 | `carts` | Shopping carts (guest + user) |
| 9 | `cart_items` | Cart contents with price snapshots |
| 10 | `orders` | Customer orders |
| 11 | `order_items` | Order items with price snapshots |
| 12 | `reviews` | Product reviews (post-purchase only) |
| 13 | `inventory_logs` | Stock movement history |
| 14 | `notifications` | User notifications |
| 15 | `failed_jobs` | Laravel default for failed jobs |

### Detailed Table Schemas

#### users table

| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| name | string | |
| email | string | unique |
| password | string | |
| role | enum | admin / customer |
| remember_token | string | |
| is_active | boolean | default true |

#### addresses table

| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| user_id | FK → users.id | |
| address_line_1 | string | |
| city | string | |
| country | string | |
| is_default | boolean | |

#### categories table

| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| name | string | |
| slug | string | unique |
| description | string | nullable |

#### brands table

| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| name | string | |
| slug | string | unique |

#### products table

| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| name | string | |
| slug | string | unique |
| description | text | |
| short_description | string | |
| category_id | FK → categories.id | |
| brand_id | FK → brands.id | |
| base_price | decimal(12,2) | |
| compare_price | decimal(12,2) | |
| stock_quantity | integer | |
| low_stock_threshold | integer | default 5 |
| is_low_stock | boolean | calculated automatically |
| sku | string | unique |
| is_featured | boolean | |
| views_count | integer | |
| images | json | |
| specifications | json | |

#### product_variants table

| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| product_id | FK → products.id | |
| name | string | example: "Red - XL" |
| attributes | json | {"color":"red", "size":"XL"} |
| price_adjustment | decimal | |
| stock_quantity | integer | |

#### coupons table

| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| code | string | unique |
| type | enum | fixed / percentage |
| value | decimal | |
| minimum_order_amount | decimal | nullable |
| start_date | date | |
| end_date | date | |

#### carts table

| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| user_id | FK → users.id | nullable |
| cookie_id | string | unique |

#### cart_items table

| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| cart_id | FK → carts.id | |
| product_id | FK → products.id | |
| product_variant_id | FK → product_variants.id | nullable |
| quantity | integer | |
| price_at_time | decimal | |

#### orders table

| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| order_number | string | unique |
| user_id | FK → users.id | |
| address_id | FK → addresses.id | |
| status | enum | |
| total | decimal | |
| coupon_code | string | nullable |

#### order_items table

| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| order_id | FK → orders.id | |
| product_variant_id | FK → product_variants.id | nullable |
| product_id | FK → products.id | |
| product_name_snapshot | string | |
| price_snapshot | decimal | |
| quantity | integer | |
| total | decimal | |

#### reviews table

| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| user_id | FK → users.id | |
| product_id | FK → products.id | |
| order_id | FK → orders.id | |
| rating | tinyint (1-5) | |
| comment | text | |
| images | json | nullable |
| is_approved | boolean | |

#### inventory_logs table

| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| product_id | FK → products.id | |
| product_variant_id | FK → product_variants.id | nullable |
| type | enum | purchase/sale/return/adjustment |
| quantity_change | integer | |
| quantity_before | integer | |
| quantity_after | integer | |

#### notifications table

| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| user_id | FK → users.id | |
| title | string | |
| message | text | |
| is_read | boolean | |

### Relationships Map


users
├── addresses (1 : many)
├── carts (1 : 1)
├── orders (1 : many)
├── reviews (1 : many)
└── notifications (1 : many)

categories └── products (1 : many)
brands └── products (1 : many)

products
├── product_variants (1 : many)
├── cart_items (1 : many)
├── order_items (1 : many)
└── reviews (1 : many)

carts └── cart_items (1 : many)
orders └── order_items (1 : many)



---

## 📡 API Documentation

### API Response Format

All endpoints return consistent JSON:

```json
{
    "success": true,
    "data": {},
    "message": "Operation successful",
    "errors": {}
}

Authentication APIs (7)
Method	Endpoint	Protection
POST	/api/register	Public
POST	/api/login	Public
POST	/api/logout	Auth + Verified
GET	/api/profile	Auth + Verified
PUT	/api/profile	Auth + Verified
POST	/api/email/verification-notification	Auth
GET	/api/verify-email/{id}/{hash}	Signed URL
Categories APIs (6)
Method	Endpoint	Access
GET	/api/categories	Public
GET	/api/categories/{id}	Public
GET	/api/categories/slug/{slug}	Public
POST	/api/categories	Admin only
PUT	/api/categories/{id}	Admin only
DELETE	/api/categories/{id}	Admin only
Brands APIs (6)
Method	Endpoint	Access
GET	/api/brands	Public
GET	/api/brands/{id}	Public
GET	/api/brands/slug/{slug}	Public
POST	/api/brands	Admin only
PUT	/api/brands/{id}	Admin only
DELETE	/api/brands/{id}	Admin only
Products APIs (9)
Method	Endpoint	Access	Description
GET	/api/products	Public	List with filters & pagination
GET	/api/products/featured	Public	Featured products
GET	/api/products/slug/{slug}	Public	Get by slug
GET	/api/products/{id}	Public	Get by ID
POST	/api/products	Admin only	Create product
PUT	/api/products/{id}	Admin only	Update product
DELETE	/api/products/{id}	Admin only	Delete product
POST	/api/products/{id}/stock	Admin only	Update stock
GET	/api/products/low-stock	Admin only	Low stock products
Cart APIs (7)
Method	Endpoint	Access	Description
GET	/api/cart	Public (Guest/User)	View cart with subtotal
POST	/api/cart/items	Public (Guest/User)	Add item to cart
PUT	/api/cart/items/{id}	Public (Guest/User)	Update quantity
DELETE	/api/cart/items/{id}	Public (Guest/User)	Remove item
DELETE	/api/cart/clear	Public (Guest/User)	Clear cart
POST	/api/cart/apply-coupon	Auth + Verified	Apply coupon
DELETE	/api/cart/coupon	Auth + Verified	Remove coupon
Addresses APIs (4)
Method	Endpoint	Access
GET	/api/addresses	Auth (User)
POST	/api/addresses	Auth (User)
PUT	/api/addresses/{id}	Auth (User)
DELETE	/api/addresses/{id}	Auth (User)
Orders APIs (6)
Method	Endpoint	Access
GET	/api/orders	Auth (User)
GET	/api/orders/{id}	Auth (User)
POST	/api/orders	Auth (User)
DELETE	/api/orders/{id}/cancel	Auth (User)
PUT	/api/orders/{id}/status	Admin only
GET	/api/admin/orders	Admin only
Reviews APIs (7)
Method	Endpoint	Access
GET	/api/products/{id}/reviews	Public
POST	/api/products/{id}/reviews	Auth (User)
GET	/api/reviews/{id}	Auth (User)
PUT	/api/reviews/{id}	Auth (User)
DELETE	/api/reviews/{id}	Auth (User)
GET	/api/admin/reviews	Admin only
PUT	/api/admin/reviews/{id}/approve	Admin only
Notifications APIs (7)
Method	Endpoint	Access
GET	/api/notifications	Auth (User)
GET	/api/notifications/unread	Auth (User)
GET	/api/notifications/unread/count	Auth (User)
GET	/api/notifications/{id}	Auth (User)
PUT	/api/notifications/{id}/read	Auth (User)
PUT	/api/notifications/read-all	Auth (User)
DELETE	/api/notifications/{id}	Auth (User)
Coupons APIs (7)
Method	Endpoint	Access
GET	/api/coupons/validate/{code}	Public
GET	/api/admin/coupons	Admin only
GET	/api/admin/coupons/active	Admin only
GET	/api/admin/coupons/{id}	Admin only
POST	/api/admin/coupons	Admin only
PUT	/api/admin/coupons/{id}	Admin only
DELETE	/api/admin/coupons/{id}	Admin only
Admin Dashboard APIs (6)
Method	Endpoint	Access	Description
GET	/api/admin/dashboard/stats	Admin only	Overall statistics
GET	/api/admin/dashboard/sales	Admin only	Sales reports (period)
GET	/api/admin/dashboard/top-products	Admin only	Best selling products
GET	/api/admin/dashboard/recent-orders	Admin only	Recent orders
GET	/api/admin/dashboard/inventory	Admin only	Inventory summary
POST	/api/admin/dashboard/clear-cache	Admin only	Clear dashboard cache
Total APIs Completed: 72 ✅
Module	Endpoints	Status
Authentication	7	✅
Categories	6	✅
Brands	6	✅
Products	9	✅
Cart	7	✅
Addresses	4	✅
Orders	6	✅
Reviews	7	✅
Notifications	7	✅
Coupons	7	✅
Dashboard	6	✅
TOTAL	72	✅
🚀 Installation & Setup
Prerequisites
PHP 8.2+

Composer

MySQL 8.0+

Node.js 18+

NPM or Yarn


Backend Setup (Laravel API)

Step 1: Clone the repository
git clone https://github.com/assim-ahmed/E-commerce_System.git
cd ecommerce_system_api


Step 2: Install PHP dependencies
composer install


Step 3: Configure environment
cp .env.example .env


Step 4: Generate application key
php artisan key:generate

Step 4: Generate application key

php artisan key:generate
Step 5: Run migrations

php artisan migrate
Step 6: Run seeders

php artisan db:seed
Default admin account:

Email: admin@example.com

Password: password

Step 7: Create storage link for images

php artisan storage:link
Step 8: Start the backend server

php artisan serve
Backend will run on: http://localhost:8000



Frontend Setup (React)
Step 1: Navigate to frontend directory

cd ecommerce-frontend
Step 2: Install dependencies

npm install
Step 3: Configure environment (if needed)
Create .env file:

env
VITE_API_URL=http://localhost:8000/api
Step 4: Start the development server

npm run dev
Frontend will run on: http://localhost:5173