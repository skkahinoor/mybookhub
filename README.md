# MyBookHub - Laravel Multi-Vendor E-Commerce Platform for Books & Academic Materials

MyBookHub is a highly specialized, production-ready Multi-Vendor E-Commerce Marketplace built on the Laravel framework. Tailored specifically for the book industry, academic materials, school packages, and used-book exchanges, the platform supports a robust five-tier role architecture (Super Admin, Vendor/Publisher, Sales Executive, Delivery Agent, and Student/Customer) and provides custom interfaces for desktop web and mobile application APIs (using Laravel Sanctum for API token-based authentication).

---

## 🚀 Key System Roles & Modules

MyBookHub's architecture centers around 5 core actor modules:

### 1. Super Admin & Staff Panel
The control center of the entire application. The Super Admin manages:
*   **User & Vendor Approvals**: Review and approve registrations for users, vendors, sales executives, and delivery agents.
*   **System Settings**: Configure commission percentages (separate for new vs. used books), Minimum Order Values (MOV) cashback, and Refer & Earn rewards.
*   **Roles & Permissions Management**: Dynamic role and permission creation for sub-admins and staff members.
*   **Payout Management**: Release vendor sales earnings and old-book cashbacks to students, and process delivery agent payout requests.
*   **Push Notifications & Marketing**: Send FCM push notifications, dynamic modal popups, and banners.
*   **Data Import/Export**: Import/Export publishers, authors, and newsletter subscribers via Excel.

### 2. Vendor & Publisher Portal
Designed for bookstores, publishers, and independent book sellers.
*   **Product Catalog Management**: Add and update books, dynamic filter values, and attributes (price, stock, discounts).
*   **POS system (Sales Concept)**: Offline sales processing tool that lets vendors lookup books by ISBN, add items to an in-store cart, apply vendor coupons/discounts, print HTML invoices, and generate PDF invoices with barcode/QR code labels.
*   **Order Fulfillment**: Track order status vs. individual item fulfillment status (allowing multi-vendor orders to be split and shipped independently).
*   **Support Queries**: Directly reply to order queries/support tickets raised by customers for their purchased items.
*   **Plan Limitations**: Vendor activities are checked against vendor subscription plans.

### 3. Ground Force / Sales Executive Module
A specialized ground-force management network to acquire and onboard institutions.
*   **Institution Onboarding**: Register educational institutions (schools, colleges, universities) into the platform.
*   **User & Vendor Onboarding**: Directly onboard and manage students and local book vendors.
*   **Earning Dashboard**: Track commission earnings generated from onboarded accounts and request wallet withdrawals.
*   **Territory Setup**: Configure state, district, and block restrictions for operation.

### 4. Delivery Agent Network (Sanctum API Powered)
Designed to run on a dedicated mobile delivery application.
*   **Registration & Location Selection**: Register and select operating zones down to Country, State, District, and Block level.
*   **Online/Offline Toggling**: Toggle agent active status in real-time.
*   **Fulfillment Pipeline**: View nearby available order delivery requests, accept/reject deliveries, update GPS coordinates, and mark orders as delivered using OTP validation.
*   **Agent Wallet**: View accumulated delivery commissions, request payouts, and view payout histories.
*   **Support Desk**: Raise and manage contact queries/support tickets with the administration.

### 5. Student & Customer Dashboard (Web & API)
For final consumers (students, parents, book readers).
*   **Academic Catalog & Book Sets**: Browse books by educational level, school board, class level, subject, language, and book type.
*   **Used Book Exchange (C2B)**: Sell old books to the platform by scanning/entering an ISBN. The platform calculates cashback based on condition parameters (Poor, Good, Like New). Students can pay a "Sell Faster" charge via Razorpay to boost their listings, mark books as sold, and receive payments into their bank accounts.
*   **Wallet, Refer & Earn**: Earn wallet credits by referring friends. Use wallet balances for purchases or request bank withdrawals.
*   **Support & Communication**: Create support tickets for orders, reply to queries, and manage FCM notifications.
*   **Profile & Addresses**: Manage profile, educational levels, academic profiles, bank accounts, and multiple shipping addresses.

---

## 🛠️ Feature Breakdown

### 📚 Academic & Catalog Management
*   **Academic Hierarchy**: Organized into Sections (Education Levels), Categories (Boards), Subcategories (Classes), and Subjects.
*   **Product Metadata**: Link products directly to Authors, Publishers, Languages, Editions, and Book Types.
*   **ISBN Lookup**: Seamless auto-fill of book titles, authors, and descriptions by matching ISBN inputs. Reduces product-upload time for both vendors and students.
*   **Dynamic AJAX Filters**: AJAX-driven catalog filters to narrow down books by Board, Class, Medium, Subject, and Price.

### 💳 POS & Checkout Engine
*   **POS In-Store Sales (Sales Concept)**: Vendors can handle walk-in sales by scanning barcodes, applying coupons, and processing instant billing.
*   **Invoicing**: Automatically generates HTML invoices and downloadable PDF invoices with barcode/QR code labels using Dompdf.
*   **Payment Gateways**: Fully integrated with PayPal, Iyzipay, and Razorpay.
*   **Flexible Order Statuses**: Double-layered tracking: System-wide Order Status (updated by Admin) and Individual Item Status (updated by Vendors).

### 🔄 Used Book Exchange (C2B / C2C)
*   **Condition Assessment**: Old book pricing and cashback values are dynamically calculated based on their condition (Poor, Fair, Good, Like New).
*   **Sell Faster Boost**: Students can opt to pay a promotional listing fee via Razorpay to boost their books' visibility in search.
*   **Direct Bank Payouts**: Super Admin verifies and initiates payouts to students' registered bank accounts once book sales are completed.

### 💰 Financial Architecture & Wallet
*   **Refer & Earn**: Referral system awarding virtual wallet credits upon successful signup.
*   **Wallet Withdrawals**: Integrated request system checking against admin-defined minimum balance rules.
*   **Vendor Commissions**: Separate, adjustable commission structures for new and old/used books.

---

## 📂 Project Structure & Routes

All entry points are decoupled into specific Laravel route files located in the `routes/` directory:

*   **[`routes/web.php`](routes/web.php)**: Web interface for Admin and Super Admin dashboards, sections/education level configurations, dynamic modals, banner sliders, coupon setups, and order queries.
*   **[`routes/vendor.php`](routes/vendor.php)**: Path for vendors containing dashboard endpoints, product catalogues, catalog attributes, POS (Sales Concept) systems, and individual item status updates.
*   **[`routes/sales.php`](routes/sales.php)**: Path for Sales Executives to register educational institutions, manage registered students, onboard vendors, view transactions, and request wallet withdrawals.
*   **[`routes/user.php`](routes/user.php)**: Web panel routes for students including dashboards, order cancellations/returns, old book listings, wallet histories, and support tickets.
*   **[`routes/userApi.php`](routes/userApi.php)**: Mobile API endpoints for the student mobile application covering home content, catalog retrieval, cart operations, checkout processing, and used book uploads.
*   **[`routes/delivery_agent_api.php`](routes/delivery_agent_api.php)**: Sanctum-protected API endpoints for delivery agents to handle registration, GPS coordinate tracking, order accepting/rejecting, and payout requests.

---

## 🔌 API Endpoints Summary

### 👤 Student Mobile API (`routes/userApi.php`)
*   `POST /api/user/register` & `verify-otp` — Registration and OTP verification.
*   `GET /api/user/home` — Banners, dynamic modals, and popular sections.
*   `GET /api/user/products` & `product-details/{id}` — Filtered book catalogs.
*   `POST /api/user/cart/add` & `delete` — Shopping cart actions.
*   `POST /api/user/checkout` & `verify` — Checkout and Razorpay payments.
*   `POST /api/user/sell-books` — List old books for cashback.
*   `POST /api/user/sell-books-faster/create-order` — Payment order for the "Sell Faster" listing feature.

### 🚴 Delivery Agent Mobile API (`routes/delivery_agent_api.php`)
*   `POST /api/delivery-agent/register` & `login` — Registration and authorization.
*   `POST /api/delivery-agent/toggle-online` — Toggles active dispatch status.
*   `GET /api/delivery-agent/available-orders` — Lists unassigned regional orders.
*   `POST /api/delivery-agent/accept-order` & `update-order-status` — Fulfillment actions.
*   `POST /api/delivery-agent/update-location` — Posts current GPS coordinates.
*   `POST /api/delivery-agent/request-payout` — Withdraws delivery fee earnings.

---

## 🛠️ Installation & Setup

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/skkahinoor/mybookhub.git
   cd mybookhub
   ```

2. **Install Dependencies**:
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Configure Environment File**:
   Copy `.env.example` to `.env` and adjust the variables with your local configuration:
   ```bash
   cp .env.example .env
   ```
   Provide your database details:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=multivendor_ecommerce
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Import Database Dump**:
   Create a database in MySQL named `multivendor_ecommerce` and import the SQL database dump:
   *   Dump file path: `database/book.sql`

5. **Start Dev Servers**:
   ```bash
   php artisan serve
   ```
   Access the frontend at `http://127.0.0.1:8000` or the Admin panel at `http://127.0.0.1:8000/admin/login`.

---

## 🔐 Credentials for Testing

*   **Superadmin Dashboard**:
    *   **Email**: `admin@admin.com`
    *   **Password**: `123456`
*   **Vendor Dashboard**:
    *   **Email**: `yasser@admin.com`
    *   **Password**: `123456`
*   **Student (Web)**:
    *   **Email**: `ibrahim@gmail.com`
    *   **Password**: `123456`
