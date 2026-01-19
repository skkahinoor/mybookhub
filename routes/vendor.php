<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\BookAttributeController;
use App\Http\Controllers\Admin\BookRequestsController;
use App\Http\Controllers\Admin\EditionController;
use App\Http\Controllers\Admin\InstitutionManagementController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\SalesExecutiveController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\OtpController;
use App\Http\Controllers\Api\InstitutionController;
use App\Http\Controllers\User\BookRequestController;
use App\Http\Controllers\Admin\SalesReportController;
use App\Http\Controllers\User\AccountController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('/vendor')->namespace('App\Http\Controllers\Admin')->group(function () {

    Route::match(['get', 'post'], 'login', 'AdminController@login')->name('vendor.login');
    // All routes in here are vendor-only (admins with type = 'vendor')
    Route::group(['middleware' => ['vendor']], function () {
            // check isbn
            Route::post('/book/isbn-lookup', [ProductsController::class, 'lookupByIsbn'])
                ->name('book.isbnLookup');
            Route::post('/book/name-suggestions', [ProductsController::class, 'nameSuggestions']);                                       // using our 'admin' guard (which we created in auth.php)
            Route::get('dashboard', 'AdminController@dashboard')->name('vendor.dashboard'); // /vendor/dashboard
            Route::get('logout', 'AdminController@logout');                                                       // Admin logout
            Route::match(['get', 'post'], 'update-vendor-password', 'AdminController@updateAdminPassword');        // GET request to view the update password <form>, and a POST request to submit the update password <form>
            Route::post('check-vendor-password', 'AdminController@checkAdminPassword');                            // Check Admin Password // This route is called from the AJAX call in admin/js/custom.js page
            Route::match(['get', 'post'], 'update-vendor-details', 'AdminController@updateAdminDetails');          // Update Admin Details in update_admin_details.blade.php page    // 'GET' method to show the update_admin_details.blade.php page, and 'POST' method for the <form> submission in the same page
            Route::match(['get', 'post'], 'update-vendor-details/{slug}', 'AdminController@updateVendorDetails');

            // Vendor Plan Settings (Admin only)
            Route::get('plan-settings', [App\Http\Controllers\Admin\PlanSettingsController::class, 'index'])->name('vendor.plan.settings');
            Route::post('plan-settings/update', [App\Http\Controllers\Admin\PlanSettingsController::class, 'update'])->name('vendor.plan.settings.update');
            Route::post('plan-settings/invite/regenerate', [App\Http\Controllers\Admin\PlanSettingsController::class, 'regenerateInviteLink'])->name('vendor.plan.settings.invite.regenerate');

            // Update the vendor's commission percentage (by the Admin) in `vendors` table (for every vendor on their own) in the Admin Panel in admin/admins/view_vendor_details.blade.php (Commissions module: Every vendor must pay a certain commission (that may vary from a vendor to another) for the website owner (admin) on every item sold, and it's defined by the website owner (admin))
            Route::post('update-vendor-commission', 'AdminController@updateVendorCommission');


            // Sales Reports (Admin)
            Route::get('reports/sales_reports', [SalesReportController::class, 'index'])->name('vendor.reports.sales_reports.index');
            Route::get('reports/sales_reports/{id}', [SalesReportController::class, 'show'])->name('vendor.reports.sales_reports.show');

            // Notifications
            Route::get('notifications', 'NotificationController@index')->name('notifications.index');
            Route::get('notifications/get', 'NotificationController@getNotifications')->name('notifications.get');
            Route::post('notifications/{id}/read', 'NotificationController@markAsRead')->name('notifications.read');
            Route::post('notifications/mark-all-read', 'NotificationController@markAllAsRead')->name('notifications.mark_all_read');

            Route::get('admins/{type?}', 'AdminController@admins');                                // In case the authenticated user (logged-in user) is superadmin, admin, subadmin, vendor these are the three Admin Management URLs depending on the slug. The slug is the `type` column in `admins` table which can only be: superadmin, admin, subadmin, or vendor    // Used an Optional Route Parameters (or Optional Route Parameters) using a '?' question mark sign, for in case that there's no any {type} passed, the page will show ALL superadmins, admins, subadmins and vendors at the same page
            Route::match(['get', 'post'], 'add-edit-admin/{id?}', 'AdminController@addEditAdmin'); // Add or Edit Admin // the slug (Route Parameter) {id?} is an Optional Parameter, so if it's passed, this means Edit/Update the Admin, and if not passed, this means Add an Admin
            Route::get('delete-admin/{id}', 'AdminController@deleteAdmin');                        // Delete an Admin
            Route::get('view-vendor-details/{id}', 'AdminController@viewVendorDetails');           // View further 'vendor' details inside Admin Management table (if the authenticated user is superadmin, admin or subadmin)
            Route::post('update-admin-status', 'AdminController@updateAdminStatus')->name('vendor.updateadminstatus');

            // otp
            // Route::get('admin/otps', [OtpController::class, 'otps'])->name('otps');

            // Update Admin Status using AJAX in admins.blade.php

            // Sections (Sections, Categories, Subcategories, Products, Attributes)
            // Route::get('sections', 'SectionController@sections');
            Route::get('sections', [SectionController::class, 'sections'])->name('sections');
            Route::post('update-section-status', 'SectionController@updateSectionStatus')->name('vendor.updatesectionstatus');               // Update Sections Status using AJAX in sections.blade.php
            Route::get('delete-section/{id}', 'SectionController@deleteSection');                        // Delete a section in sections.blade.php
            Route::match(['get', 'post'], 'add-edit-section/{id?}', 'SectionController@addEditSection'); // the slug {id?} is an Optional Parameter, so if it's passed, this means Edit/Update the section, and if not passed, this means Add a Section

            // Categories
            Route::get('categories', 'CategoryController@categories');                                      // Categories in Catalogue Management in Admin Panel
            Route::post('update-category-status', 'CategoryController@updateCategoryStatus')->name('vendor.updatecategorystatus');               // Update Categories Status using AJAX in categories.blade.php
            Route::match(['get', 'post'], 'add-edit-category/{id?}', 'CategoryController@addEditCategory'); // the slug {id?} is an Optional Parameter, so if it's passed, this means Edit/Update the Category, and if not passed, this means Add a Category
            Route::get('append-categories-level', 'CategoryController@appendCategoryLevel');                // Show Categories <select> <option> depending on the chosen Section (show the relevant categories of the chosen section) using AJAX in admin/js/custom.js in append_categories_level.blade.php page
            Route::get('delete-category/{id}', 'CategoryController@deleteCategory');                        // Delete a category in categories.blade.php
            Route::get('delete-category-image/{id}', 'CategoryController@deleteCategoryImage');             // Delete a category image in add_edit_category.blade.php from BOTH SERVER (FILESYSTEM) & DATABASE


            //Publishers
            Route::get('publisher', 'PublisherController@publisher');
            Route::post('update-publisher-status', 'PublisherController@updatePublisherStatus')->name('vendor.updatepublisherstatus'); // Update Publisher Status using AJAX in publisher.blade.php

            Route::post('/admin/add-publisher-ajax', [App\Http\Controllers\Admin\PublisherController::class, 'addPublisherAjax'])->name('vendor.addPublisherAjax');

            // Authors
            Route::post('update-author-status', [AuthorController::class, 'updateStatus'])->name('vendor.updateauthorstatus');

            // Subjects
            Route::post('update-subject-status', [SubjectController::class, 'updateStatus'])->name('vendor.updatesubjectstatus');

            // Update Brands Status using AJAX in brands.blade.php
            Route::get('delete-publisher/{id}', 'PublisherController@deletePublisher');                        // Delete a brand in brands.blade.php
            Route::match(['get', 'post'], 'add-edit-publisher/{id?}', 'PublisherController@addEditPublisher'); // the slug {id?} is an Optional Parameter, so if it's passed, this means Edit/Update the brand, and if not passed, this means Add a Brand

            //Author
            Route::get('authors', [AuthorController::class, 'index'])->name('author');
            Route::get('add_author', [AuthorController::class, 'add'])->name('add.author');
            Route::post('store_author', [AuthorController::class, 'store'])->name('store.author');
            Route::get('author_edit/{id}', [AuthorController::class, 'edit'])->name('edit.author');
            Route::post('author_update/{id}', [AuthorController::class, 'update'])->name('update.author');
            Route::get('author_delete/{id}', [AuthorController::class, 'delete'])->name('delete.author');

            //RequestedBooks
            Route::get('requestedbooks', [BookRequestsController::class, 'index'])->name('requestbook.index');
            Route::match(['get', 'post'], 'requestedbooks/reply/{id}', [BookRequestsController::class, 'reply'])->name('requestbook.reply');
            Route::delete('book-requests/{id}', [BookRequestsController::class, 'delete'])->name('bookrequests.delete');
            Route::post('admin/bookrequests/update-status', [BookRequestsController::class, 'updateStatus'])->name('vendor.bookrequests.updateStatus');

            //Subject
            Route::get('subjects', [SubjectController::class, 'index'])->name('subject');
            Route::get('add_subject', [SubjectController::class, 'add'])->name('add.subject');
            Route::post('store_subject', [SubjectController::class, 'store'])->name('store.subject');
            Route::get('edit/{id}', [SubjectController::class, 'edit'])->name('edit.subject');
            Route::post('update/{id}', [SubjectController::class, 'update'])->name('update.subject');
            Route::get('delete/{id}', [SubjectController::class, 'delete'])->name('delete.subject');

            // Schools
            Route::resource('schools', SchoolController::class)->names([
                'index'   => 'vendor.schools.index',
                'create'  => 'vendor.schools.create',
                'store'   => 'vendor.schools.store',
                'show'    => 'vendor.schools.show',
                'edit'    => 'vendor.schools.edit',
                'update'  => 'vendor.schools.update',
                'destroy' => 'vendor.schools.destroy',
            ]);

            // Institution Management
            Route::resource('institution-managements', 'InstitutionManagementController')->names([
                'index'   => 'vendor.institution_managements.index',
                'create'  => 'vendor.institution_managements.create',
                'store'   => 'vendor.institution_managements.store',
                'show'    => 'vendor.institution_managements.show',
                'edit'    => 'vendor.institution_managements.edit',
                'update'  => 'vendor.institution_managements.update',
                'destroy' => 'vendor.institution_managements.destroy',
            ]);
            Route::post('update-institution-status', [InstitutionManagementController::class, 'updateStatus'])->name('vendor.institution_managements.update_status');
            Route::get('institution-management/{id}/details', [InstitutionManagementController::class, 'getDetails'])->name('institution_managements.get_details');

            // Cities removed

            // Blocks Management
            Route::resource('blocks', 'BlockController')->names([
                'index'   => 'vendor.blocks.index',
                'create'  => 'vendor.blocks.create',
                'store'   => 'vendor.blocks.store',
                'edit'    => 'vendor.blocks.edit',
                'update'  => 'vendor.blocks.update',
                'destroy' => 'vendor.blocks.destroy',
            ]);
            Route::post('update-block-status', 'BlockController@updateStatus');

            // Students Management
            Route::resource('students', 'StudentController')->names([
                'index'   => 'vendor.students.index',
                'create'  => 'vendor.students.create',
                'store'   => 'vendor.students.store',
                'show'    => 'vendor.students.show',
                'edit'    => 'vendor.students.edit',
                'update'  => 'vendor.students.update',
                'destroy' => 'vendor.students.destroy',
            ]);
            Route::get('students/{id}/details', 'StudentController@details')->name('vendor.students.details');
            Route::post('students/{id}/update-status', 'StudentController@updateStatus')->name('vendor.students.updateStatus');

            // Withdrawals Management
            Route::get('withdrawals', 'WithdrawalController@index')->name('vendor.withdrawals.index');
            Route::get('withdrawals/{id}', 'WithdrawalController@show')->name('vendor.withdrawals.show');
            Route::post('withdrawals/{id}/update-status', 'WithdrawalController@updateStatus')->name('vendor.withdrawals.updateStatus');
            Route::post('withdrawals/minimum/update', 'WithdrawalController@updateMinimum')->name('vendor.withdrawals.minimum.update');

            // AJAX route for getting classes based on institution type (outside admin middleware for AJAX access)
            Route::get('institution-classes', [InstitutionManagementController::class, 'getClasses'])->name('vendor.institution.classes');


            // AJAX route for getting location data based on pincode (outside admin middleware for AJAX access)
            Route::get('institution-location-data', [App\Http\Controllers\Admin\InstitutionManagementController::class, 'getLocationData'])->name('institution_location_data');


            // Products (with vendor plan check middleware)
            Route::middleware(['vendor.plan'])->group(function () {
                Route::get('products/getauthors', [ProductsController::class, 'getAuthor']);
                Route::get('products', [ProductsController::class, 'products']);                                        // render products.blade.php in the Admin Panel
                Route::post('update-product-status', [ProductsController::class, 'updateProductStatus'])->name('vendor.updateproductstatus');               // Update Products Status using AJAX in products.blade.php
                Route::get('delete-product/{id}', [ProductsController::class, 'deleteProduct']);                        // Delete a product in products.blade.php
                Route::get('delete-product-attribute/{id}', [ProductsController::class, 'deleteProductAttribute']);     // Delete a product attribute (ProductsAttribute)
                Route::match(['get', 'post'], 'add-edit-product/{id?}', [ProductsController::class, 'addEditProduct'])->name('vendor.products.add'); // the slug (Route Parameter) {id?} is an Optional Parameter, so if it's passed, this means 'Edit/Update the Product', and if not passed, this means' Add a Product'    // GET request to render the add_edit_product.blade.php view, and POST request to submit the <form> in that view
                Route::post('save-product-attributes', [ProductsController::class, 'saveProductAttributes'])->name('vendor.products.saveAttributes'); // Save product attributes (stock and discount) via AJAX
                Route::get('delete-product-image/{id}', [ProductsController::class, 'deleteProductImage']);             // Delete a product images (in the three folders: small, medium and large) in add_edit_product.blade.php page from BOTH SERVER (FILESYSTEM) & DATABASE
                Route::get('delete-product-video/{id}', [ProductsController::class, 'deleteProductVideo']);             // Delete a product video in add_edit_product.blade.php page from BOTH SERVER (FILESYSTEM) & DATABASE
            });

            // Attributes
            Route::match(['get', 'post'], 'add-edit-attributes/{id}', [ProductsController::class, 'addAttributes']); // GET request to render the add_edit_attributes.blade.php view, and POST request to submit the <form> in that view
            Route::post('update-attribute-status', [ProductsController::class, 'updateAttributeStatus']);            // Update Attributes Status using AJAX in add_edit_attributes.blade.php
            Route::get('delete-attribute/{id}', [ProductsController::class, 'deleteAttribute']);                     // Delete an attribute in add_edit_attributes.blade.php
            Route::match(['get', 'post'], 'edit-attributes/{id}', [ProductsController::class, 'editAttributes']);    // in add_edit_attributes.blade.php

            // Images
            Route::match(['get', 'post'], 'add-images/{id}', [ProductsController::class, 'addImages']); // GET request to render the add_edit_attributes.blade.php view, and POST request to submit the <form> in that view
            Route::post('update-image-status', [ProductsController::class, 'updateImageStatus']);       // Update Images Status using AJAX in add_images.blade.php
            Route::get('delete-image/{id}', [ProductsController::class, 'deleteImage']);                // Delete an image in add_images.blade.php

            // Banners
            Route::get('banners', 'BannerController@banners');
            Route::post('update-banner-status', 'BannerController@updateBannerStatus');               // Update Categories Status using AJAX in banners.blade.php
            Route::get('delete-banner/{id}', 'BannerController@deleteBanner');                        // Delete a banner in banners.blade.php
            Route::match(['get', 'post'], 'add-edit-banner/{id?}', 'BannerController@addEditBanner'); // the slug (Route Parameter) {id?} is an Optional Parameter, so if it's passed, this means 'Edit/Update the Banner', and if not passed, this means' Add a Banner'    // GET request to render the add_edit_banner.blade.php view, and POST request to submit the <form> in that view

            // Filters
            Route::get('filters', 'FilterController@filters');                                                   // Render filters.blade.php page
            Route::post('update-filter-status', 'FilterController@updateFilterStatus');                          // Update Filter Status using AJAX in filters.blade.php
            Route::post('update-filter-value-status', 'FilterController@updateFilterValueStatus');               // Update Filter Value Status using AJAX in filters_values.blade.php
            Route::get('filters-values', 'FilterController@filtersValues');                                      // Render filters_values.blade.php page
            Route::match(['get', 'post'], 'add-edit-filter/{id?}', 'FilterController@addEditFilter');            // the slug (Route Parameter) {id?} is an Optional Parameter, so if it's passed, this means 'Edit/Update the filter', and if not passed, this means' Add a filter'    // GET request to render the add_edit_filter.blade.php view, and POST request to submit the <form> in that view
            Route::match(['get', 'post'], 'add-edit-filter-value/{id?}', 'FilterController@addEditFilterValue'); // the slug (Route Parameter) {id?} is an Optional Parameter, so if it's passed, this means 'Edit/Update the Filter Value', and if not passed, this means' Add a Filter Value'    // GET request to render the add_edit_filter_value.blade.php view, and POST request to submit the <form> in that view
            Route::post('category-filters', 'FilterController@categoryFilters');                                 // Show the related filters depending on the selected category <select> in category_filters.blade.php (which in turn is included by add_edit_product.php) using AJAX. Check admin/js/custom.js

            // Coupons (with vendor plan check middleware)
            Route::middleware(['vendor.plan'])->group(function () {
                Route::get('coupons', 'CouponsController@coupons');                          // Render admin/coupons/coupons.blade.php page in the Admin Panel
                Route::post('update-coupon-status', 'CouponsController@updateCouponStatus')->name('vendor.updatecouponstatus'); // Update Coupon Status (active/inactive) via AJAX in admin/coupons/coupons.blade.php, check admin/js/custom.js
                Route::get('delete-coupon/{id}', 'CouponsController@deleteCoupon');          // Delete a Coupon via AJAX in admin/coupons/coupons.blade.php, check admin/js/custom.js
                Route::match(['get', 'post'], 'add-edit-coupon/{id?}', 'CouponsController@addEditCoupon'); // the slug (Route Parameter) {id?} is an Optional Parameter, so if it's passed, this means 'Edit/Update the Coupon', and if not passed, this means' Add a Coupon'    // GET request to render the add_edit_coupon.blade.php view (whether Add or Edit depending on passing or not passing the Optional Parameter {id?}), and POST request to submit the <form> in that same page
            });

            // Users
            Route::get('users', 'UserController@users');                          // Render admin/users/users.blade.php page in the Admin Panel
            Route::post('update-user-status', 'UserController@updateUserStatus')->name('vendor.updateuserstatus'); // Update User Status (active/inactive) via AJAX in admin/users/users.blade.php, check admin/js/custom.js

            // Orders
            // Render admin/orders/orders.blade.php page (Orders Management section) in the Admin Panel
            Route::get('orders', 'OrderController@orders');

            // Render admin/orders/order_details.blade.php (View Order Details page) when clicking on the View Order Details icon in admin/orders/orders.blade.php (Orders tab under Orders Management section in Admin Panel)
            Route::get('orders/{id}', 'OrderController@orderDetails');

            // Sales Concept (Vendor only)
            Route::get('sales-concept', 'OrderController@salesConcept');
            Route::post('sales-concept/search-isbn', 'OrderController@searchBookByIsbn');
            Route::post('sales-concept/add-to-cart', 'OrderController@addToSalesCart');
            Route::post('sales-concept/remove-from-cart', 'OrderController@removeFromSalesCart');
            Route::post('sales-concept/process-sale', 'OrderController@processSale');

            // Update Order Status (which is determined by 'admin'-s ONLY, not 'vendor'-s, in contrast to "Update Item Status" which can be updated by both 'vendor'-s and 'admin'-s) (Pending, Shipped, In Progress, Canceled, ...) in admin/orders/order_details.blade.php in Admin Panel
            // Note: The `order_statuses` table contains all kinds of order statuses (that can be updated by 'admin'-s ONLY in `orders` table) like: pending, in progress, shipped, canceled, ...etc. In `order_statuses` table, the `name` column can be: 'New', 'Pending', 'Canceled', 'In Progress', 'Shipped', 'Partially Shipped', 'Delivered', 'Partially Delivered' and 'Paid'. 'Partially Shipped': If one order has products from different vendors, and one vendor has shipped their product to the customer while other vendor (or vendors) didn't!. 'Partially Delivered': if one order has products from different vendors, and one vendor has shipped and DELIVERED their product to the customer while other vendor (or vendors) didn't!    // The `order_item_statuses` table contains all kinds of order statuses (that can be updated by both 'vendor'-s and 'admin'-s in `orders_products` table) like: pending, in progress, shipped, canceled, ...etc.
            Route::post('update-order-status', 'OrderController@updateOrderStatus');

            // Update Item Status (which can be determined by both 'vendor'-s and 'admin'-s, in contrast to "Update Order Status" which is updated by 'admin'-s ONLY, not 'vendor'-s) (Pending, In Progress, Shipped, Delivered, ...) in admin/orders/order_details.blade.php in Admin Panel
            // Note: The `order_statuses` table contains all kinds of order statuses (that can be updated by 'admin'-s ONLY in `orders` table) like: pending, in progress, shipped, canceled, ...etc. In `order_statuses` table, the `name` column can be: 'New', 'Pending', 'Canceled', 'In Progress', 'Shipped', 'Partially Shipped', 'Delivered', 'Partially Delivered' and 'Paid'. 'Partially Shipped': If one order has products from different vendors, and one vendor has shipped their product to the customer while other vendor (or vendors) didn't!. 'Partially Delivered': if one order has products from different vendors, and one vendor has shipped and DELIVERED their product to the customer while other vendor (or vendors) didn't!    // The `order_item_statuses` table contains all kinds of order statuses (that can be updated by both 'vendor'-s and 'admin'-s in `orders_products` table) like: pending, in progress, shipped, canceled, ...etc.
            Route::post('update-order-item-status', 'OrderController@updateOrderItemStatus');

            // Orders Invoices
            // Render order invoice page (HTML) in order_invoice.blade.php
            Route::get('orders/invoice/{id}', 'OrderController@viewOrderInvoice');

            // Render order PDF invoice in order_invoice.blade.php using Dompdf Package
            Route::get('orders/invoice/pdf/{id}', 'OrderController@viewPDFInvoice');

            // Shipping Charges module
            // Render the Shipping Charges page (admin/shipping/shipping_charges.blade.php) in the Admin Panel for 'admin'-s only, not for vendors
            Route::get('shipping-charges', 'ShippingController@shippingCharges');

            // Update Shipping Status (active/inactive) via AJAX in admin/shipping/shipping_charages.blade.php, check admin/js/custom.js
            Route::post('update-shipping-status', 'ShippingController@updateShippingStatus')->name('vendor.updateshippingstatus');

            // Render admin/shipping/edit_shipping_charges.blade.php page in case of HTTP 'GET' request ('Edit/Update Shipping Charges'), or hadle the HTML Form submission in the same page in case of HTTP 'POST' request
            Route::match(['get', 'post'], 'edit-shipping-charges/{id}', 'ShippingController@editShippingCharges');

            // Newsletter Subscribers module
            // Render admin/subscribers/subscribers.blade.php page (Show all Newsletter subscribers in the Admin Panel)
            Route::get('subscribers', 'NewsletterController@subscribers');

            // Update Subscriber Status (active/inactive) via AJAX in admin/subscribers/subscribers.blade.php, check admin/js/custom.js
            Route::post('update-subscriber-status', 'NewsletterController@updateSubscriberStatus')->name('vendor.updatesubscriberstatus');

            // Delete a Subscriber via AJAX in admin/subscribers/subscribers.blade.php, check admin/js/custom.js
            Route::get('delete-subscriber/{id}', 'NewsletterController@deleteSubscriber');

            // Export subscribers (`newsletter_subscribers` database table) as an Excel file using Maatwebsite/Laravel Excel Package in admin/subscribers/subscribers.blade.php
            Route::get('export-subscribers', 'NewsletterController@exportSubscribers');

            // User Ratings & Reviews
            // Render admin/ratings/ratings.blade.php page in the Admin Panel
            Route::get('ratings', 'RatingController@ratings');

            // Update Rating Status (active/inactive) via AJAX in admin/ratings/ratings.blade.php, check admin/js/custom.js
            Route::post('update-rating-status', 'RatingController@updateRatingStatus')->name('vendor.updateratingstatus');

            // Delete a Rating via AJAX in admin/ratings/ratings.blade.php, check admin/js/custom.js
            Route::get('delete-rating/{id}', 'RatingController@deleteRating');

            // Languages Routes
            Route::get('languages', 'App\Http\Controllers\Admin\LanguageController@languages');
            Route::post('update-language-status', 'App\Http\Controllers\Admin\LanguageController@updateLanguageStatus')->name('vendor.updatelanguagestatus'); // Update Language Status using AJAX in languages.blade.php
            Route::match(['get', 'post'], 'add-edit-language/{id?}', 'App\Http\Controllers\Admin\LanguageController@addEditLanguage');
            Route::get('delete-language/{id}', 'App\Http\Controllers\Admin\LanguageController@deleteLanguage');

            // Ebooks Management
            Route::get('ebooks', [App\Http\Controllers\Admin\EbooksController::class, 'index'])->name('admin.ebooks.index');
            Route::get('add-edit-ebook/{id?}', [App\Http\Controllers\Admin\EbooksController::class, 'create'])->name('admin.ebooks.create');
            Route::post('add-edit-ebook/{id?}', [App\Http\Controllers\Admin\EbooksController::class, 'store'])->name('admin.ebooks.store');
            Route::get('delete-ebook/{id}', [App\Http\Controllers\Admin\EbooksController::class, 'destroy'])->name('admin.ebooks.delete');

            // Editions
            Route::resource('edition', EditionController::class)->names([
                'index'   => 'edition.index',
                'create'  => 'edition.create',
                'store'   => 'edition.store',
                'edit'    => 'edition.edit',
                'update'  => 'edition.update',
                'destroy' => 'edition.destroy',
            ])->except(['show']);

            Route::get('product/{id}/editions', [BookAttributeController::class, 'getEditions']);
            Route::post('book-attribute', [BookAttributeController::class, 'store']);

            // Contact Us Queries
            Route::get('contact-queries', 'AdminController@contactQueries');
            Route::post('update-contact-status', 'AdminController@updateContactStatus');
            Route::match(['get', 'post'], 'contact-queries/reply/{id}', 'AdminController@updateContactReply');
            Route::get('delete-contact-query/{id}', 'AdminController@deleteContactQuery');
        });
    });
