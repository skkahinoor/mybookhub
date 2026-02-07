# Role-Based Permission System Implementation

## Overview
This document outlines the comprehensive role-based permission system implemented across the MyBookHub application using Spatie Laravel Permission package.

## Permissions Structure

### All Available Permissions by Module

#### 1. **Admins Module**
- `view_admins` - View admin list
- `add_admins` - Create new admins
- `edit_admins` - Edit admin details
- `delete_admins` - Delete admins
- `update_admins_status` - Activate/deactivate admins

#### 2. **Vendors Module**
- `view_vendors` - View vendor list
- `add_vendors` - Create new vendors
- `edit_vendors` - Edit vendor details
- `delete_vendors` - Delete vendors
- `update_vendor_details` - Update vendor profile
- `update_vendor_commission` - Modify vendor commission rates
- `update_vendors_status` - Activate/deactivate vendors

#### 3. **Products Module**
- `view_products` - View product list
- `add_products` - Create new products
- `edit_products` - Edit product details
- `delete_products` - Delete products
- `update_product_status` - Activate/deactivate products

#### 4. **Catalogue Module**
- `view_sections` - View book sections
- `view_categories` - View categories
- `add_categories` - Create categories
- `edit_categories` - Edit categories
- `delete_categories` - Delete categories
- `view_publishers` - View publishers
- `view_authors` - View authors
- `view_subjects` - View subjects
- `view_languages` - View languages
- `view_editions` - View editions
- `view_coupons` - View coupons
- `view_requested_books` - View book requests

#### 5. **Orders Module**
- `view_orders` - View order list
- `edit_orders` - Edit order details
- `delete_orders` - Delete orders
- `update_order_status` - Update overall order status
- `update_order_item_status` - Update individual item status
- `view_sales_concept` - Access sales concept page

#### 6. **Ratings Module**
- `view_ratings` - View product ratings and reviews
- `update_ratings_status` - Approve/reject ratings

#### 7. **Users Module**
- `view_users` - View user list
- `update_users_status` - Activate/deactivate users
- `view_subscribers` - View newsletter subscribers
- `view_contact_queries` - View contact form submissions

#### 8. **Institutions Module**
- `view_institutions` - View institution list
- `add_institutions` - Create institutions
- `edit_institutions` - Edit institutions
- `delete_institutions` - Delete institutions
- `view_blocks` - View blocks

#### 9. **Students Module**
- `view_students` - View student list
- `add_students` - Create students
- `edit_students` - Edit students
- `delete_students` - Delete students

#### 10. **Withdrawals Module**
- `view_withdrawals` - View withdrawal requests
- `update_withdrawals_status` - Approve/reject withdrawals

#### 11. **Banners Module**
- `view_banners` - View banner list
- `add_banners` - Create banners
- `edit_banners` - Edit banners
- `delete_banners` - Delete banners

#### 12. **Shipping Module**
- `view_shipping` - View shipping charges
- `edit_shipping` - Edit shipping charges

#### 13. **OTP Module**
- `view_otp` - View OTP management

#### 14. **Sales Module**
- `view_sales` - View sales executives
- `add_sales` - Create sales executives
- `edit_sales` - Edit sales executives
- `delete_sales` - Delete sales executives
- `sales_dashboard` - Access sales dashboard
- `update_sales_status` - Activate/deactivate sales executives

#### 15. **Roles Module**
- `view_roles` - View roles list
- `add_roles` - Create new roles
- `edit_roles` - Edit roles and permissions
- `delete_roles` - Delete roles

#### 16. **Reports Module**
- `view_reports` - View reports
- `export_reports` - Export reports

#### 17. **Settings Module**
- `view_settings` - View settings
- `update_settings` - Update settings

## Implementation Details

### Controllers with Permission Checks

1. **ProductsController.php**
   - `deleteProduct()` - Checks `delete_products`
   - `updateProductStatus()` - Checks `update_product_status`

2. **AdminController.php**
   - `updateAdminStatus()` - Checks `update_admins_status` or `update_vendors_status`
   - `deleteAdmin()` - Checks `delete_admins` or `delete_vendors`

3. **SalesExecutiveController.php**
   - `index()` - Checks `view_sales`
   - `addEdit()` - Checks `add_sales` or `edit_sales`
   - `delete()` - Checks `delete_sales`
   - `updateStatus()` - Checks `update_sales_status`

4. **UserController.php**
   - `users()` - Checks `view_users`
   - `updateUserStatus()` - Checks `update_users_status`

5. **OrderController.php**
   - `orders()` - Checks `view_orders`
   - `updateOrderStatus()` - Checks `update_order_status`
   - `updateOrderItemStatus()` - Checks `update_order_item_status`

### Sidebar Menu Protection

All sidebar menu items are now wrapped with `@can` directives:

```blade
@can('view_admins')
    <li class="nav-item">
        <a href="{{ url('admin/admins/admin') }}">Admins</a>
    </li>
@endcan
```

This ensures that menu items are only visible to users with the appropriate permissions.

## Default Role Configuration

### Admin Role
- Has **ALL** permissions by default
- Full access to entire system

### Vendor Role
- Created but no default permissions
- Permissions must be assigned by admin

### Sales Role
- Created but no default permissions
- Permissions must be assigned by admin

### User Role
- Created but no default permissions
- Permissions must be assigned by admin

### Student Role
- Created but no default permissions
- Permissions must be assigned by admin

## How to Use

### For Administrators

1. **Navigate to Roles & Permissions**
   - Go to Admin Management > Roles & Permissions

2. **Edit a Role**
   - Click "Edit" on any role
   - Check/uncheck permissions as needed
   - Permissions are grouped by module for easy management

3. **Assign Roles to Users**
   - When creating/editing users, select their role
   - The role determines what they can access

### For Developers

#### Check Permission in Controller
```php
if (!Auth::guard('admin')->user()->can('delete_products')) {
    abort(403, 'Unauthorized action.');
}
```

#### Check Permission in Blade
```blade
@can('delete_products')
    <button>Delete</button>
@endcan
```

#### Check Permission in Routes
```php
Route::get('/products', [ProductsController::class, 'index'])
    ->middleware('can:view_products');
```

## Database Structure

### Permissions Table
- `id` - Primary key
- `name` - Permission name (e.g., 'view_products')
- `module` - Module grouping (e.g., 'Products')
- `guard_name` - Always 'web'

### Roles Table
- `id` - Primary key
- `name` - Role name (e.g., 'admin', 'vendor')
- `guard_name` - Always 'web'

### Model Has Permissions
- Links users to their permissions

### Model Has Roles
- Links users to their roles

### Role Has Permissions
- Links roles to their permissions

## Testing the System

1. **Create a Test Role**
   ```
   - Go to Admin Management > Roles & Permissions
   - Click "Add Role"
   - Name it "Test Manager"
   - Select only specific permissions (e.g., view_products, view_orders)
   - Save
   ```

2. **Assign Role to User**
   ```
   - Edit an admin/vendor user
   - Assign the "Test Manager" role
   - Save
   ```

3. **Login as That User**
   ```
   - Only permitted menu items will be visible
   - Attempting to access restricted pages will show 403 error
   ```

## Security Features

1. **Controller-Level Protection**
   - All sensitive actions check permissions before executing

2. **UI-Level Protection**
   - Menu items hidden if user lacks permission
   - Buttons/links hidden with @can directives

3. **Route-Level Protection** (Optional)
   - Can add middleware to routes for extra security

4. **Vendor Isolation**
   - Vendors can only manage their own products
   - Additional checks ensure data ownership

## Maintenance

### Adding New Permissions

1. Update `RolesAndPermissionsSeeder.php`:
   ```php
   'NewModule' => ['view_newmodule', 'add_newmodule', 'edit_newmodule'],
   ```

2. Run seeder:
   ```bash
   php artisan db:seed --class=RolesAndPermissionsSeeder
   ```

3. Add controller checks:
   ```php
   if (!Auth::guard('admin')->user()->can('view_newmodule')) {
       abort(403);
   }
   ```

4. Add blade protection:
   ```blade
   @can('view_newmodule')
       <!-- content -->
   @endcan
   ```

### Removing Permissions

1. Remove from seeder
2. Run seeder to update
3. Remove from controllers and views
4. Manually delete from database if needed

## Notes

- All permissions use the 'web' guard
- Admin role automatically gets all new permissions
- Permission changes take effect immediately
- No need to logout/login after permission changes
- Cache is automatically cleared when permissions are updated
