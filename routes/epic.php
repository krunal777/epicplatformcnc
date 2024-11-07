<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Epic\EpicOrderController;
use App\Http\Controllers\Epic\EpicPageController;
use App\Http\Controllers\Epic\EpicProductController;
use App\Http\Controllers\Epic\EpicSearchController;
use App\Http\Controllers\Epic\EpicCartController;
use App\Http\Controllers\Epic\EpicUsersController;
use App\Http\Controllers\Epic\EpicBrandController;
use App\Http\Controllers\Epic\DdiTaxController;
use App\Http\Controllers\Epic\BulkPriceRuleController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::middleware(['custom.auth.guard'])->prefix('epic')->group(static function (): void {
    Route::get('/dashboard', [EpicOrderController::class, 'index']);
    Route::get('/search-product', [EpicSearchController::class, 'searchProducts']);
    Route::get('/get_fields_page', [EpicPageController::class, 'get_fields_by_page_slug']);
    Route::get('/get_simple_products', [EpicProductController::class, 'get_simple_products_list']);
    Route::get('/get_store_data', [EpicPageController::class, 'getStoreData']);
    Route::get('/get_cart_data', [EpicCartController::class, 'getCartData']);
    Route::post('/update_cart_summary', [EpicCartController::class, 'updateCartSummary']);

    // Use PUT for full update, PATCH for partial update
    Route::put('/products/{id}', [EpicProductController::class, 'update']);
    Route::patch('/products/variants/{id}', [EpicProductController::class, 'update_variants']);

    //show 5 sale products for home page
    Route::get('/get_sale_products', [EpicProductController::class, 'getSaleProducts']);

    Route::post('/email-smtp', [EpicPageController::class, 'emailSmtpSend']);
    Route::get('/sitemap', [EpicPageController::class, 'sitemap']);
    Route::get('/tax-exemption', [EpicUsersController::class, 'getTaxExemption']);
    Route::post('/tax-exemption', [EpicUsersController::class, 'storeTaxExemption']);
    Route::get('/category-sitemap', [EpicPageController::class, 'categorySitemap']);
    Route::get('/product-sitemap', [EpicPageController::class, 'productSitemap']);
    Route::get('/brand-sitemap', [EpicPageController::class, 'brandsSitemap']);

    // Route for DDI Tax
    Route::get('/ddi-tax-rate-zipcode', [DdiTaxController::class, 'getDDITaxrateZipcode']);
    
    // Bulk price rules
    Route::post('/bulk-price-insert', [BulkPriceRuleController::class, 'store']);
    Route::post('/bulk-price-update/{id}', [BulkPriceRuleController::class, 'update']); // Use PUT for update with ID
    Route::delete('/bulk-price-delete/{id}', [BulkPriceRuleController::class, 'destroy']);
    Route::get('/bulk-price-list', [BulkPriceRuleController::class, 'getList']);
    Route::get('/bulk-price-detail/{id}', [BulkPriceRuleController::class, 'getDetail']);

    //DDI validate route
    Route::post('/ddi-validate-user', [DdiTaxController::class, 'ddiValidateUser']);
    Route::post('/ddi-submit-order', [DdiTaxController::class, 'ddiSubmitOrder']);
    Route::post('/ddi-fetch-price', [DdiTaxController::class, 'ddiFetchPrice']);
    Route::get('/shippo-get-method', [DdiTaxController::class, 'shippoGetMethod']);
    
    

    // Brand page products list
    Route::get('/brand-detail/{name}', [EpicBrandController::class, 'getBrandDetail']);
    Route::get('/brand-categories-list/{id}', [EpicProductController::class, 'getBrandCategoriesList']);
    Route::get('/get-products-brand-categories-list/{id}', [EpicProductController::class, 'getProductsByBrandAndCategories']);
});