<?php

use App\Models\ticket;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pageController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\campaingController;
use App\Http\Controllers\WithdrawController;
use App\Http\Controllers\Admin\chatController;
use App\Http\Controllers\subscriptionController;
use App\Http\Controllers\Admin\attributeController;
use App\Http\Controllers\Admin\CustomOrderController;
use App\Http\Controllers\Admin\Ecommerce\TagController;
use App\Http\Controllers\Admin\Ecommerce\AuthController;
use App\Http\Controllers\Admin\Ecommerce\SizeController;
use App\Http\Controllers\Admin\Ecommerce\StafController;
use App\Http\Controllers\Admin\Ecommerce\BrandController;
use App\Http\Controllers\Admin\Ecommerce\ColorController;
use App\Http\Controllers\Admin\Ecommerce\OrderController;
use App\Http\Controllers\Admin\Ecommerce\CouponController;
use App\Http\Controllers\Admin\Ecommerce\SliderController;
use App\Http\Controllers\Admin\Ecommerce\ticketController;
use App\Http\Controllers\Admin\Ecommerce\VendorController;
use App\Http\Controllers\blogControler as ablogController;
use App\Http\Controllers\Admin\Ecommerce\ClassicController;
use App\Http\Controllers\Admin\Ecommerce\ProductController;
use App\Http\Controllers\Admin\Ecommerce\ProfileController;
use App\Http\Controllers\Admin\Ecommerce\SettingController;
use App\Http\Controllers\Admin\Ecommerce\CategoryController;
use App\Http\Controllers\Admin\Ecommerce\CustomerController;
use App\Http\Controllers\Admin\Ecommerce\DashboardController;
use App\Http\Controllers\Admin\Ecommerce\CollectionController;
use App\Http\Controllers\Admin\Ecommerce\SubCategoryController;
use App\Http\Controllers\Admin\Ecommerce\DeliveryTrackingController;

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

Route::middleware(['auth', 'admin'])->group(function () {

    // System Update
    Route::get('/update', [SystemController::class, 'updateIndex'])->name('updateIndex');
    Route::post('/update', [SystemController::class, 'update'])->name('update');
    Route::get('/php_info', [SystemController::class, 'php_info'])->name('info');

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('category', CategoryController::class);
    Route::resource('collection', CollectionController::class);
    Route::resource('sub-category', SubCategoryController::class);
    Route::resource('slider', SliderController::class);
    Route::resource('brand', BrandController::class);
    Route::resource('color', ColorController::class);
    Route::resource('size', SizeController::class)->except('show');
    Route::resource('tag', TagController::class)->except('show');
    Route::resource('coupon', CouponController::class);

    Route::post('/n-category', [CategoryController::class, 'nCat'])->name('ncat');
    Route::post('/n-scategory', [SubCategoryController::class, 'nsCat'])->name('nscat');
    Route::post('/n-mcategory', [SubCategoryController::class, 'nmCat'])->name('nmcat');
    Route::post('/n-color', [ColorController::class, 'ncolor'])->name('ncolor');
    Route::post('/n-tag', [TagController::class, 'ntag'])->name('ntag');
    Route::post('/n-size', [SizeController::class, 'nsize'])->name('nsize');
    Route::get('commente/delete/{id}', [ProductController::class, 'commetn_delte'])->name('comment');
    Route::get('rating/delete/{id}', [ProductController::class, 'rating_delte'])->name('rating');
    Route::get('rating/edit/{id}', [ProductController::class, 'rating_edit'])->name('rating.edit');
    Route::post('rating/update', [ProductController::class, 'rating_update'])->name('rating.update');
    Route::get('idelte/{id}', [ProductController::class, 'idelte'])->name('idelte');
    Route::get('category/product/{id}', [ProductController::class, 'megCatProduct'])->name('category.product');
    Route::get('sub-category/product/{id}', [ProductController::class, 'subCatProduct'])->name('sub-category.product');
    Route::get('min-category/product/{id}', [ProductController::class, 'minCatProduct'])->name('min-category.product');
    Route::get('ex-category/product/{id}', [ProductController::class, 'exCatProduct'])->name('ex-category.product');
    
    // Low Stock Products Route
    Route::get('low-stock-products', [ProductController::class, 'lowStock'])->name('low.product');

    Route::resource('author', AuthController::class);
    Route::resource('customer', CustomerController::class);
    Route::resource('vendor', VendorController::class);
    Route::get('vendor/change-pass/{id}', [VendorController::class, 'change_passIndex'])->name('vendor.change_pass_index');
    Route::put('vendor/change-pass/{id}', [VendorController::class, 'change_pass'])->name('vendor.change_pass');
    Route::get('vendor/product/{vid}', [VendorController::class, 'Vproduct'])->name('vendor.product');
    Route::get('vendor_status/{id}', [VendorController::class, 'vendor_status'])->name('vendor_status');
    Route::get('vendor/withdraw/list', [WithdrawController::class, 'allwithlist'])->name('vendor.withdraw');

    Route::get('vendor/withdraw/{id}', [WithdrawController::class, 'aprove'])->name('vendor.withdraw.aprove');
    Route::get('vendor/withdrawc/{id}', [WithdrawController::class, 'cancel'])->name('vendor.withdraw.cancel');
    Route::get('vendor/withdrawd/{id}', [WithdrawController::class, 'delete'])->name('vendor.withdraw.delete');
    Route::get('pages', [pageController::class, 'index'])->name('pages');
    Route::get('page/create', [pageController::class, 'form'])->name('page.create');
    Route::get('page/delete/{did}', [pageController::class, 'delete'])->name('page.delete');
    Route::get('page/edit/{edit}', [pageController::class, 'edit'])->name('page.edit');
    Route::post('page/new/create', [pageController::class, 'store'])->name('page.make');
    Route::post('page/new/update', [pageController::class, 'update'])->name('page.update');

    Route::post('order/refud', [OrderController::class, 'refund'])->name('refund');
    Route::post('order/refud_to', [OrderController::class, 'refund_two'])->name('refund_2');
    Route::get('comission/', [OrderController::class, 'comission'])->name('comission');
    Route::get('staf/create', [StafController::class, 'create'])->name('staff.create');
    Route::Post('staf/store', [StafController::class, 'store'])->name('staff.store');
    Route::get('staf/list', [StafController::class, 'stafflist'])->name('staff.list');

    Route::get('staf/{id}/edit', [StafController::class, 'staffEdit'])->name('staff.edit');
    Route::get('mail', [ticketController::class, 'mail'])->name('mail');
    Route::get('mail/show/{id}', [ticketController::class, 'mailShow'])->name('mail.show');
    Route::get('mail/{id}', [ticketController::class, 'maildelete'])->name('mail.delete');

    Route::get('subscribe', [subscriptionController::class, 'show'])->name('subscribe');
    Route::get('ticket', [ticketController::class, 'index'])->name('ticket');
    Route::get('ticket/{ticket}', [ticketController::class, 'delete'])->name('ticket.delete');
    Route::get('ticket/show/{show}', [ticketController::class, 'show'])->name('ticket.show');
    Route::post('ticket/update', [ticketController::class, 'update'])->name('ticket.update');
    Route::get('get/product/image/{id}', [ProductController::class, 'getProductImage']);
    Route::post('update/product/image', [ProductController::class, 'updateImage'])->name('update.image');
    Route::delete('delete/product/image/{id}', [ProductController::class, 'deleteImage']);
    Route::get('product/status/{id}', [ProductController::class, 'status'])->name('product.status');
    Route::get('user/status/{id}', [CustomerController::class, 'status'])->name('user.status');
    Route::get('product/active', [ProductController::class, 'activeProduct'])->name('product.active');
    Route::get('product/disable', [ProductController::class, 'disableProduct'])->name('product.disable');
    Route::get('product/approve/{id}', [ProductController::class, 'approveProduct'])->name('product.approved');
    Route::get('product/unaproved', [ProductController::class, 'unaprovedProduct'])->name('product.unaproved');

    Route::get('product/reached', [ProductController::class, 'reachedProduct'])->name('product.reached');
    Route::post('get/sub-categories', [ProductController::class, 'subCategory']);
    Route::post('get/mini-categories', [ProductController::class, 'miniCategory']);
    Route::post('get/extra-categories', [ProductController::class, 'extraCategory']);
    Route::get('delete/product/download/{id}', [ProductController::class, 'deleteDownloadFile']);
    Route::post('update/product/download', [ProductController::class, 'updateDownloadFile']);
    Route::get('product/type', [ProductController::class, 'type'])->name('product.type');
    Route::get('product/inhouse/create', [ProductController::class, 'inhouseCreate'])->name('product.inhouse.create');

    Route::get('admin/product/color/{cc}/{pp}', [ProductController::class, 'nColorDelete'])->name('color.delete.n2');
    Route::get('admin/product/attr/{cc}', [ProductController::class, 'nattrDelete'])->name('attr.delete.n2');

    Route::get('product/bluk', [ProductController::class, 'imex'])->name('product.imex');
    Route::get('product/export', [ProductController::class, 'export'])->name('product.export');
    Route::Post('product/import', [ProductController::class, 'import'])->name('product.import');
    Route::get('classic/list', [ClassicController::class, 'index'])->name('classic.index');
    Route::get('classic/form', [ClassicController::class, 'form'])->name('classic.form');
    Route::get('classic/status/{id}', [ClassicController::class, 'status'])->name('classic.status');
    Route::delete('/classic/{id}', [ClassicController::class, 'delete']);
    Route::get('classic/{ads}/edit', [ClassicController::class, 'editC'])->name('ads.edit');
    Route::Post('/ads/create', [ClassicController::class, 'storeC'])->name('product.clasified.create');
    Route::Post('/ads/update', [ClassicController::class, 'update'])->name('product.clasified.update');

    Route::resource('product', ProductController::class);

    Route::get('mini-categories', [SubCategoryController::class, 'minicategory'])->name('minicategory');
    Route::get('mini-categories/list', [SubCategoryController::class, 'minicategoryList'])->name('minicategory.list');
    Route::get('mini-categories/delete/{did}', [SubCategoryController::class, 'minicategoryDelete'])->name('minicategory.delete');
    Route::get('mini-categories/edit/{edit}', [SubCategoryController::class, 'minicategoryEdit'])->name('minicategory.edit');
    Route::post('mini-categories/create', [SubCategoryController::class, 'minicategoryStore'])->name('create.mini');
    Route::post('mini-categories/edit', [SubCategoryController::class, 'minicategoryUpdate'])->name('edit.mini');

    Route::get('extra-categories', [SubCategoryController::class, 'extracategory'])->name('extracategory');
    Route::post('extra-categories/create', [SubCategoryController::class, 'extracategoryStore'])->name('create.extra');
    Route::get('extra-categories/list', [SubCategoryController::class, 'extracategoryList'])->name('extracategory.list');
    Route::get('extra-categories/delete/{did}', [SubCategoryController::class, 'extracategoryDelete'])->name('extracategory.delete');
    Route::get('extra-categories/edit/{edit}', [SubCategoryController::class, 'extracategoryEdit'])->name('extracategory.edit');
    Route::post('extra-categories/edit', [SubCategoryController::class, 'extracategoryUpdate'])->name('edit.extra');

    Route::post('product/order', [CustomOrderController::class, 'orderProductStore'])->name('product.order.store');
    Route::get('product/order/{id}', [CustomOrderController::class, 'orderProduct'])->name('product.order');
    Route::get('apply/coupon/{code}/{id}', [CustomOrderController::class, 'applyCoupon'])->name('apply.coupon');
    Route::get('gallery', [ProductController::class, 'gallery'])->name('gallery');
    Route::get('pay/order/{id}', [CustomOrderController::class, 'pay'])->name('order.pay');
    Route::get('campaing/list', [campaingController::class, 'index'])->name('campaing.index');
    Route::get('campaing/status/{id}', [campaingController::class, 'status'])->name('campaing.status');
    Route::get('campaing/edit/{id}', [campaingController::class, 'edit'])->name('campaing.edit');
    Route::get('campaing/comment/{id}', [campaingController::class, 'getCOmments'])->name('campaing.comment');
    Route::delete('campaing/delete/{id}', [campaingController::class, 'delete'])->name('campaing.delete');
    Route::delete('campaing/comment/delete/{id}', [campaingController::class, 'deleteComment'])->name('campaing.comment.delete');
    Route::get('campaing/remove/{id}', [campaingController::class, 'remove'])->name('campaing.remove');
    Route::get('campaing/create', [campaingController::class, 'create'])->name('campaing.create');
    Route::Post('campaing/create/data', [campaingController::class, 'getData'])->name('campaing.getData');
    Route::Post('campaing/store', [campaingController::class, 'store'])->name('campaing.store');
    Route::Post('campaing/update', [campaingController::class, 'update'])->name('campaing.update');

    // Enhanced Order Controller Routes with BD Courier Integration
    Route::group(['as' => 'order.', 'prefix' => 'order'], function () {
        // Main order listing (AJAX-enabled with search)
        Route::get('/', [OrderController::class, 'index'])->name('index');
        
        // Analytics and Data Routes
        Route::get('/analytics', [OrderController::class, 'getAnalytics'])->name('analytics');
        Route::get('/search', [OrderController::class, 'searchOrders'])->name('search');
        
        // Enhanced Phone History Route with BD Courier Integration
        Route::get('/phone-history', [OrderController::class, 'getPhoneHistory'])->name('phone-history');
        Route::post('/phone-history', [OrderController::class, 'getPhoneHistory'])->name('phone-history.post');
        
        // BD Courier API Test Routes (for admin debugging)
        Route::get('/courier/test', function() {
            $service = new \App\Services\BdCourierService();
            
            return response()->json([
                'configured' => $service->isConfigured(),
                'test_result' => $service->testConnection()
            ]);
        })->name('courier.test');
        
        Route::get('/courier/history/{phone}', function($phone) {
            $service = new \App\Services\BdCourierService();
            return response()->json($service->getCourierHistory($phone));
        })->name('courier.history');
        
        Route::get('/courier/risk/{phone}', function($phone) {
            $service = new \App\Services\BdCourierService();
            return response()->json($service->getRiskAssessment($phone));
        })->name('courier.risk');
        
        // Specialized order status lists (BEFORE {id} routes)
        Route::get('pending', [OrderController::class, 'pending'])->name('pending');
        Route::get('pre', [OrderController::class, 'pre'])->name('pre');
        Route::get('processing', [OrderController::class, 'processing'])->name('processing');
        Route::get('cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::get('delivered', [OrderController::class, 'delivered'])->name('delivered');
        Route::get('partials', [OrderController::class, 'partials'])->name('partials');
        Route::get('partials/status/{id}/{st}', [OrderController::class, 'partialStatus'])->name('partials.status');
        
        // Order actions (BEFORE {id} routes)
        Route::get('print/{id}', [OrderController::class, 'print'])->name('print');
        Route::get('invoice/{id}', [OrderController::class, 'invoice'])->name('invoice');
        Route::get('delete/{did}', [OrderController::class, 'delete'])->name('delete');

        // Status change routes (BEFORE {id} routes)
        Route::get('status/pending/{id}', [OrderController::class, 'statusPending'])->name('status.pending');
        Route::get('status/processing/{id}', [OrderController::class, 'statusProcessing'])->name('status.processing');
        Route::get('status/cancel/{id}', [OrderController::class, 'statusCancel'])->name('status.cancel');
        Route::get('status/delivered/{id}', [OrderController::class, 'statusDelivered'])->name('status.delivered');
        Route::get('status/shipping/{id}', [OrderController::class, 'statusShipping'])->name('status.shipping');
        Route::get('status/return-accept/{id}', [OrderController::class, 'returnAccept'])->name('status.return-accept');
        Route::get('status/return-complete/{id}', [OrderController::class, 'returnComplete'])->name('status.return-complete');
        Route::get('status/sub/{id}/{status}/{vendor}', [OrderController::class, 'sub_status'])->name('subStatus');
        
        // Order CRUD operations (AFTER specific routes)
        Route::get('{id}', [OrderController::class, 'show'])->name('show');
        Route::put('{id}', [OrderController::class, 'update'])->name('update');
        Route::post('{id}/update', [OrderController::class, 'update'])->name('update.post');
        
        // POST routes for bulk operations and SMS
        Route::post('bulk-status-update', [OrderController::class, 'bulkStatusUpdate'])->name('bulk-status-update');
        Route::post('send-sms', [OrderController::class, 'sendCustomSms'])->name('send.sms');
        
        // Courier integration routes
        Route::post('bulk-send-to-courier', [CourierController::class, 'bulkSendToCourier'])->name('bulk-send-to-courier');
    });

    // Delivery Tracking Routes (Enhanced)
    Route::group(['as' => 'delivery.', 'prefix' => 'delivery'], function () {
        Route::get('/', [DeliveryTrackingController::class, 'index'])->name('index');
        Route::get('/show/{id}', [DeliveryTrackingController::class, 'show'])->name('show');
        Route::post('/send-to-courier/{id}', [DeliveryTrackingController::class, 'sendToCourier'])->name('send-courier');
        Route::post('/update-status/{id}', [DeliveryTrackingController::class, 'updateStatus'])->name('update-status');
        Route::post('/bulk-update', [DeliveryTrackingController::class, 'bulkUpdateStatus'])->name('bulk-update');
        Route::get('/balance', [DeliveryTrackingController::class, 'getBalance'])->name('balance');
        Route::get('/export', [DeliveryTrackingController::class, 'exportReport'])->name('export');
        
        // BD Courier specific routes
        Route::post('/courier/check-history', function(\Illuminate\Http\Request $request) {
            $service = new \App\Services\BdCourierService();
            $phone = $request->input('phone');
            
            if (!$phone) {
                return response()->json(['success' => false, 'message' => 'Phone number required']);
            }
            
            return response()->json([
                'courier_history' => $service->getCourierHistory($phone),
                'risk_assessment' => $service->getRiskAssessment($phone)
            ]);
        })->name('courier.check-history');
    });

    Route::get('/blogs', [ablogController::class, 'index'])->name('index');
    Route::get('/user-blogs', [ablogController::class, 'index2'])->name('user_blog');
    Route::get('/Create-new-blog', [ablogController::class, 'new_blog_form'])->name('new_blog');
    Route::post('/create-blog', [ablogController::class, 'store'])->name('create_blog');

    Route::get('blog/status/{id}', [ablogController::class, 'status'])->name('blog.status');
    Route::delete('/blog-delete/{id}', [ablogController::class, 'destory'])->name('blog_delete');
    Route::get('/blog-edit/{id}', [ablogController::class, 'blog_edit_form'])->name('blog_edit');
    Route::post('/blog-update', [ablogController::class, 'update_exit_blog'])->name('update_exit_blog');

    // Auth User Profile Define Here....
    Route::group(['as' => 'profile.', 'prefix' => 'profile'], function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('update', [ProfileController::class, 'showUpdateProfileForm'])->name('update');
        Route::put('info', [ProfileController::class, 'updateInfo'])->name('update.info');
        Route::put('info2', [ProfileController::class, 'updateInfo2'])->name('update.info2');
        Route::get('change-password', [ProfileController::class, 'showChangePasswordForm'])->name('change.password');
        Route::put('password/update', [ProfileController::class, 'updatePassword'])->name('password.update');
    });
    Route::Post('/get/attributes', [attributeController::class, 'getAttribute']);

    Route::group(['as' => 'attribute.', 'prefix' => 'attribute'], function () {
        Route::get('/list', [attributeController::class, 'index'])->name('index');
        Route::get('/form', [attributeController::class, 'form'])->name('form');
        Route::Post('/store', [attributeController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [attributeController::class, 'edit']);
        Route::put('/{id}', [attributeController::class, 'update']);
        Route::delete('/delete/{id}', [attributeController::class, 'delete']);
        Route::get('/value/{id}', [attributeController::class, 'value'])->name('value');
        Route::Post('/value/store', [attributeController::class, 'storeValue'])->name('value.store');
        Route::delete('/value/delete/{id}', [attributeController::class, 'deleteValue']);
        Route::get('/value/{id}/edit', [attributeController::class, 'editValue']);
        Route::Post('/value/update', [attributeController::class, 'updateValue'])->name('value.update');
    });

    Route::group(['as' => 'connection.', 'prefix' => 'connection'], function () {
        Route::get('/live-chat', [ChatController::class, 'index'])->name('live.chat');
        Route::get('/live-chat/new-sms/count', [ChatController::class, 'countNewMessage'])->name('live.chat.new-sms.count');
        Route::get('/live-chat-user-list', [ChatController::class, 'liveChatUserList'])->name('live.chat.user.list');
        Route::get('/live-chat-list', [ChatController::class, 'liveChatList'])->name('live.chat.list');
        Route::get('/live-chat-list/{id}', [ChatController::class, 'liveChatListById']);
        Route::post('/live-chat', [ChatController::class, 'storeLiveChatForm'])->name('store.chat');
        Route::get('/live-chat/status/{id}', [ChatController::class, 'updateStatus']);
    });

    Route::get('shop', [SettingController::class, 'showShop'])->name('shop');
    Route::put('shop/update', [SettingController::class, 'shopUpdate'])->name('shop.update');

    // Application Setting Route Define Here....
    Route::get('setting', [SettingController::class, 'index'])->name('setting');
    Route::put('setting', [SettingController::class, 'update'])->name('setting.update');
    Route::put('setting/logo', [SettingController::class, 'updateLogo'])->name('update.logo');

    Route::get('notice', [SettingController::class, 'noticeIndex'])->name('notice_index');

    Route::get('setting/site_info', [SettingController::class, 'shop_infoIndex'])->name('setting.site_info');
    Route::get('setting/layout', [SettingController::class, 'layoutIndex'])->name('setting.layout');
    Route::get('setting/shop_settings', [SettingController::class, 'shopSettingsIndex'])->name('setting.shop_settings');
    Route::get('setting/color', [SettingController::class, 'colorIndex'])->name('setting.color');
    Route::get('setting/header', [SettingController::class, 'headerIndex'])->name('setting.header');
    Route::get('setting/seo', [SettingController::class, 'seoIndex'])->name('setting.seo');
    Route::get('setting/courier', [SettingController::class, 'courierIndex'])->name('setting.courier');
    Route::post('setting/courier/sendsteedfast', [CourierController::class, 'sendsteedfast'])->name('setting.courier.sendsteedfast');

    // BD Courier Settings & Testing Routes
    Route::group(['as' => 'setting.courier.', 'prefix' => 'setting/courier'], function () {
        Route::get('/bd-courier', function() {
            $service = new \App\Services\BdCourierService();
            return view('admin.settings.bd-courier', [
                'configured' => $service->isConfigured(),
                'api_key' => env('BD_COURIER_API_KEY') ? substr(env('BD_COURIER_API_KEY'), 0, 10) . '...' : 'Not Set'
            ]);
        })->name('bd-courier');
        
        Route::post('/bd-courier/test', function(\Illuminate\Http\Request $request) {
            $service = new \App\Services\BdCourierService();
            $phone = $request->input('phone', '01700000000');
            
            return response()->json([
                'configured' => $service->isConfigured(),
                'test_connection' => $service->testConnection(),
                'sample_data' => $service->getCourierHistory($phone)
            ]);
        })->name('bd-courier.test');
    });

    Route::get('setting/mailsmsapireglog', [SettingController::class, 'mailsmsapireglogIndex'])->name('setting.mailsmsapireglog');

    Route::get('setting/getway', [SettingController::class, 'getway'])->name('setting.getway');
    Route::post('setting/getway', [SettingController::class, 'setting_g'])->name('setting_g');
    Route::get('setting/docs', [SettingController::class, 'docs'])->name('setting.docs');
    Route::get('setting/home', [SettingController::class, 'home'])->name('setting.home');

    // Enhanced SMS Management Routes
    Route::group(['as' => 'sms.', 'prefix' => 'sms'], function () {
        Route::get('logs', [App\Http\Controllers\Admin\Ecommerce\SmsLogController::class, 'index'])->name('logs.index');
        Route::get('logs/{id}', [App\Http\Controllers\Admin\Ecommerce\SmsLogController::class, 'show'])->name('logs.show');
        Route::delete('logs/{id}', [App\Http\Controllers\Admin\Ecommerce\SmsLogController::class, 'destroy'])->name('logs.destroy');
        Route::post('logs/bulk-delete', [App\Http\Controllers\Admin\Ecommerce\SmsLogController::class, 'bulkDelete'])->name('logs.bulk-delete');
        Route::post('logs/clear-old', [App\Http\Controllers\Admin\Ecommerce\SmsLogController::class, 'clearOld'])->name('logs.clear-old');
        Route::get('dashboard', [App\Http\Controllers\Admin\Ecommerce\SmsLogController::class, 'dashboard'])->name('dashboard');
        Route::get('test', [App\Http\Controllers\Admin\Ecommerce\SmsLogController::class, 'testForm'])->name('test');
        Route::post('test', [App\Http\Controllers\Admin\Ecommerce\SmsLogController::class, 'sendTest'])->name('test.send');
        
        // SMS Templates for Orders
        Route::get('templates', function() {
            return view('admin.sms.templates');
        })->name('templates');
    });

    // Debug and Testing Routes (Remove in production)
    Route::group(['as' => 'debug.', 'prefix' => 'debug'], function () {
        Route::get('/courier-test', function() {
            $service = new \App\Services\BdCourierService();
            
            $phones = ['01700000000', '01800000000', '01900000000'];
            $results = [];
            
            foreach ($phones as $phone) {
                $results[$phone] = [
                    'history' => $service->getCourierHistory($phone),
                    'risk' => $service->getRiskAssessment($phone)
                ];
            }
            
            return response()->json([
                'service_configured' => $service->isConfigured(),
                'connection_test' => $service->testConnection(),
                'sample_results' => $results
            ]);
        })->name('courier-test');
        
        Route::get('/clear-courier-cache', function() {
            \Illuminate\Support\Facades\Cache::flush();
            return response()->json(['message' => 'Courier cache cleared']);
        })->name('clear-courier-cache');
    });
});