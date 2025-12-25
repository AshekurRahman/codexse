# Codexse Marketplace - Implementation Plan

## Project Overview
Build a premium digital marketplace (similar to UI8.net) using Laravel 11 full-stack with Livewire 3, Alpine.js, and Tailwind CSS.

## Current Environment
- **PHP:** 8.4.12 (exceeds 8.2+ requirement)
- **Composer:** 2.8.12
- **Node.js:** 22.21.1 (exceeds 20+ requirement)
- **npm:** 10.9.4
- **Directory:** `/Applications/AMPPS/www/codexse`

## Project Structure Decision
Based on the specification, this should be a **single Laravel full-stack application** (not separate backend/frontend). The existing empty `backend` and `frontend` folders should be removed, and Laravel should be installed at the root level.

---

## Phase 1: Foundation Setup

### 1.1 Laravel Project Initialization
- [ ] Remove empty backend/frontend folders
- [ ] Install Laravel 11 fresh in the codexse directory
- [ ] Configure environment variables (.env)
- [ ] Setup MySQL database connection
- [ ] Install and configure required packages:
  - `livewire/livewire` (v3)
  - `laravel/breeze` (authentication scaffolding)
  - `stripe/stripe-php` + `laravel/cashier` (payments)
  - `spatie/laravel-permission` (roles & permissions)
  - `spatie/laravel-medialibrary` (file handling)
  - `spatie/laravel-sluggable` (URL slugs)
  - `spatie/laravel-activitylog` (audit logging)
  - `intervention/image` (image processing)

### 1.2 Frontend Assets Setup
- [ ] Configure Tailwind CSS with dark mode (`darkMode: 'class'`)
- [ ] Install Alpine.js with `@alpinejs/persist` plugin
- [ ] Setup Vite configuration
- [ ] Create CSS custom properties for theme colors

### 1.3 Theme System Implementation
- [ ] Create Alpine.js theme toggle component
- [ ] Define light/dark color palette CSS variables
- [ ] Configure Tailwind for dark mode classes
- [ ] Implement system preference detection

---

## Phase 2: Database Architecture

### 2.1 Core Migrations
```
users                 - User accounts with roles
sellers               - Seller profiles & Stripe Connect
products              - Digital products
categories            - Product categories (with parent_id for nesting)
tags                  - Product tags
product_tag           - Pivot table
orders                - Customer orders
order_items           - Individual items in orders
reviews               - Product reviews
downloads             - Download tracking
payouts               - Seller payout requests
coupons               - Discount codes
wishlists             - User wishlists
messages              - Buyer-seller messaging
notifications         - In-app notifications
settings              - Site configuration
pages                 - CMS pages
```

### 2.2 Models & Relationships
- [ ] User (hasOne Seller, hasMany Orders, Reviews, Downloads)
- [ ] Seller (belongsTo User, hasMany Products, Payouts)
- [ ] Product (belongsTo Seller, Category; hasMany Reviews, OrderItems)
- [ ] Category (self-referential for subcategories)
- [ ] Order (belongsTo User, hasMany OrderItems)
- [ ] OrderItem (belongsTo Order, Product; generates License)
- [ ] Review (belongsTo User, Product)
- [ ] Payout (belongsTo Seller)
- [ ] Coupon (usage tracking)

### 2.3 Database Seeders
- [ ] RoleSeeder (customer, seller, admin)
- [ ] CategorySeeder (UI Kits, Templates, Icons, etc.)
- [ ] AdminUserSeeder
- [ ] DemoProductSeeder (for development)

---

## Phase 3: Authentication & Authorization

### 3.1 Authentication System
- [ ] Install Laravel Breeze with Blade + Livewire
- [ ] Registration with email verification
- [ ] Login with remember me
- [ ] Password reset flow
- [ ] Optional 2FA implementation
- [ ] Social login (Google, GitHub) using Laravel Socialite

### 3.2 Authorization & Roles
- [ ] Setup Spatie Laravel Permission
- [ ] Define roles: customer, seller, admin
- [ ] Create middleware: `IsSeller`, `IsAdmin`
- [ ] Permission-based access control for seller/admin features

### 3.3 User Profile Management
- [ ] Profile editing (avatar, bio, social links)
- [ ] Account settings
- [ ] Notification preferences
- [ ] Account deletion (GDPR compliance)

---

## Phase 4: Seller System

### 4.1 Seller Registration
- [ ] Seller application form
- [ ] Admin approval workflow
- [ ] Seller profile creation
- [ ] Store name & slug management

### 4.2 Seller Dashboard
- [ ] Dashboard overview (sales, earnings, views)
- [ ] Sales analytics charts
- [ ] Recent orders list
- [ ] Pending reviews

### 4.3 Product Management (Seller)
- [ ] Create product form (Livewire multi-step)
- [ ] Product listing with filters
- [ ] Edit product
- [ ] Version management & changelog
- [ ] Product status management (draft, pending, published)

### 4.4 Stripe Connect Integration
- [ ] Seller onboarding to Stripe Connect
- [ ] Account status verification
- [ ] Payout request submission
- [ ] Payout history view

---

## Phase 5: Product System

### 5.1 Product CRUD
- [ ] Product model with all fields
- [ ] File upload system (secure storage)
- [ ] Preview images handling
- [ ] Product variations (license types)
- [ ] Version history

### 5.2 Category System
- [ ] Categories with subcategories (nested)
- [ ] Category icons/images
- [ ] Software compatibility tags (Figma, Sketch, etc.)

### 5.3 Product Display
- [ ] Product listing page with grid/list view
- [ ] Product detail page
- [ ] Image gallery with lightbox
- [ ] Live preview link
- [ ] Related products
- [ ] Seller info card

### 5.4 Search & Discovery
- [ ] Full-text search with Laravel Scout
- [ ] Livewire-powered filters:
  - Category
  - Price range
  - Software compatibility
  - License type
  - Rating
- [ ] Sorting (newest, popular, price)
- [ ] Search suggestions/autocomplete

---

## Phase 6: Shopping & Checkout

### 6.1 Shopping Cart
- [ ] Cart service (session-based for guests, DB for users)
- [ ] Add/remove items (Livewire)
- [ ] Cart persistence across sessions
- [ ] License type selection

### 6.2 Wishlist
- [ ] Wishlist toggle button (Livewire)
- [ ] Wishlist page
- [ ] Move to cart functionality

### 6.3 Checkout Flow
- [ ] Cart review
- [ ] Coupon code application
- [ ] Guest checkout option
- [ ] Stripe Payment Intent integration
- [ ] Order creation on successful payment

### 6.4 Stripe Integration
- [ ] Payment Intent creation
- [ ] Stripe Elements for card input
- [ ] Webhook handling for payment confirmation
- [ ] Failed payment handling

---

## Phase 7: Order & Download System

### 7.1 Order Management
- [ ] Order confirmation page
- [ ] Order history (user dashboard)
- [ ] Order detail view
- [ ] PDF invoice generation

### 7.2 License System
- [ ] License key generation (unique per purchase)
- [ ] License types (Personal, Commercial, Extended)
- [ ] License verification API endpoint

### 7.3 Download System
- [ ] Secure download URLs (signed, expiring)
- [ ] Download tracking
- [ ] Download limit enforcement
- [ ] Re-download access for purchases

### 7.4 Notifications
- [ ] Order confirmation email
- [ ] Product update notification
- [ ] Download ready notification

---

## Phase 8: Reviews & Ratings

### 8.1 Review System
- [ ] Star rating (1-5)
- [ ] Written review
- [ ] Verified purchase badge
- [ ] Review submission (Livewire)

### 8.2 Review Moderation
- [ ] Admin approval queue
- [ ] Seller response capability
- [ ] Helpful vote system
- [ ] Report inappropriate review

---

## Phase 9: Admin Panel

### 9.1 Admin Dashboard
- [ ] Overview stats (users, products, orders, revenue)
- [ ] Charts (sales trends, signups)
- [ ] Recent activity feed

### 9.2 User Management
- [ ] User listing with search/filter
- [ ] User detail view
- [ ] Role assignment
- [ ] Ban/suspend users

### 9.3 Seller Management
- [ ] Seller applications queue
- [ ] Approve/reject sellers
- [ ] Seller performance overview
- [ ] Commission rate management

### 9.4 Product Moderation
- [ ] Pending products queue
- [ ] Approve/reject with feedback
- [ ] Featured product selection
- [ ] Product editing (admin override)

### 9.5 Order Management
- [ ] Order listing
- [ ] Order details
- [ ] Refund processing

### 9.6 Financial Management
- [ ] Payout requests queue
- [ ] Approve/process payouts
- [ ] Commission reports
- [ ] Revenue reports

### 9.7 Content Management
- [ ] Categories CRUD
- [ ] Coupon CRUD
- [ ] Static pages (CMS)
- [ ] Email template management

### 9.8 Settings
- [ ] Site settings (name, logo, etc.)
- [ ] Commission rates
- [ ] Email configuration
- [ ] Stripe settings

---

## Phase 10: SEO & Performance

### 10.1 SEO
- [ ] Meta tags (title, description)
- [ ] Open Graph tags
- [ ] XML sitemap generation
- [ ] Schema.org markup for products

### 10.2 Performance
- [ ] Database indexing
- [ ] Query optimization
- [ ] Caching (config, routes, views)
- [ ] Image optimization (WebP, lazy loading)
- [ ] Asset bundling with Vite

---

## Implementation Order

### Step 1: Project Setup (Start Here)
1. Create fresh Laravel 11 installation
2. Configure database and environment
3. Install core packages
4. Setup Tailwind + Alpine.js + Livewire
5. Implement theme system (light/dark)
6. Create base layouts

### Step 2: Database & Auth
1. Run all migrations
2. Setup roles and permissions
3. Implement authentication (Breeze)
4. Create user profile management

### Step 3: Core Product System
1. Categories CRUD
2. Product CRUD (seller side)
3. Product listing & detail pages
4. Search and filters

### Step 4: E-commerce
1. Shopping cart
2. Checkout with Stripe
3. Order processing
4. Download system

### Step 5: Seller Features
1. Seller registration/approval
2. Seller dashboard
3. Stripe Connect
4. Payouts

### Step 6: Reviews & Social
1. Review system
2. Wishlist
3. Messaging

### Step 7: Admin Panel
1. Dashboard
2. All management features
3. Reports

### Step 8: Polish
1. SEO optimization
2. Performance tuning
3. Testing
4. Documentation

---

## Key Files to Create First

```
app/
├── Models/
│   ├── User.php (modify)
│   ├── Seller.php
│   ├── Product.php
│   ├── Category.php
│   └── Order.php
├── Livewire/
│   ├── ThemeToggle.php
│   ├── ProductSearch.php
│   ├── ShoppingCart.php
│   └── AddToCart.php
├── Http/
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── ProductController.php
│   │   ├── Seller/DashboardController.php
│   │   └── Admin/DashboardController.php
│   └── Middleware/
│       ├── IsSeller.php
│       └── IsAdmin.php

resources/views/
├── layouts/
│   ├── app.blade.php
│   ├── seller.blade.php
│   └── admin.blade.php
├── components/
│   ├── navbar.blade.php
│   ├── footer.blade.php
│   └── product-card.blade.php
├── pages/
│   ├── home.blade.php
│   └── products/
└── livewire/
    └── theme-toggle.blade.php
```

---

## Questions for Clarification

Before proceeding, please confirm:

1. **Database:** Should I create a new MySQL database called `codexse`, or do you have existing database credentials to use?

2. **Directory Structure:** The spec mentions a single Laravel full-stack app. Should I remove the empty `backend` and `frontend` folders and install Laravel at the root?

3. **Starting Point:** Should I begin with Phase 1 (Foundation Setup) - installing Laravel and setting up the base project structure?

4. **Admin Panel:** Would you prefer to use **Filament** (pre-built admin panel) or build a custom admin panel with Blade/Livewire?

5. **Stripe:** Do you have Stripe API keys ready, or should I set up the structure for later configuration?

---

## Ready to Proceed

Once confirmed, I will:
1. Initialize the Laravel 11 project
2. Install all required packages
3. Setup Tailwind CSS with dark mode
4. Configure Livewire 3 and Alpine.js
5. Create the base layout with theme toggle
6. Begin database migrations

Let me know which approach you'd like for the admin panel and any other preferences!
