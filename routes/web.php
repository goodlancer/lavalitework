<?php

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
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
});

Route::get('/', 'PublicController@home');
Route::get('faq','PublicController@faq');
Route::get('verifyemail/{id}', 'PublicController@verifyemail');
Route::get('contact', 'PublicController@contact');
Route::get('privacy', 'PublicController@privacy');

Route::get('blogs', 'PublicController@blogs');
Route::get('workwithus', 'PublicController@workwithus');

Route::get('services', 'PublicController@services');

Route::get('products', 'PublicController@products');
Route::get('products/{slug}', 'PublicController@products_cat');



Route::post('advanced_filters', 'PublicController@advanced_filters');
Route::post('getquickview', 'PublicController@getquickview');


Route::get('single-products', 'PublicController@singleproducts');
Route::get('single-products/{slug}', 'PublicController@singleproducts');

Route::get('singlepost', 'PublicController@singlepost');
Route::get('auth', 'PublicController@auth');


Route::get('locations', 'PublicController@locations');
Route::get('about', 'PublicController@about');
Route::get('single-post/{slug}', 'PublicController@singlepost');
Route::get('single-location/{slug}', 'PublicController@singlelocation');
Route::get('why', 'PublicController@why');
Route::get('about-us', 'PublicController@about');
Route::get('elements', 'PublicController@elements');
Route::get('team/{slug}', 'PublicController@team');
Route::get('login', 'PublicController@logins');
Route::get('register', 'PublicController@registers');
Route::get('cart', 'CheckoutController@cart');
Route::get('cart/{slug}', 'CheckoutController@cart');
//cropper

// Route::get('paypal', 'PaymentController@index');
// Route::post('paypal', 'PaymentController@payWithpaypal');

Route::get('status', 'PayPalController@getPaymentStatus');

Route::post('contactus', 'PublicController@contactformsubmit');
Route::post('subscribeus', 'PublicController@subscribeussubmit');

Route::get('testimonials', 'PublicController@testimonial');

Route::post('productsearch', 'PublicController@productsearchsubmit');


Route::post('checkemail', 'PublicController@checkemail');
Route::post('register_action', 'PublicController@registers_action');

Route::post('update_cart', 'CheckoutController@update_cart_action');
Route::post('search_zipcode', 'CheckoutController@search_zipcode');


Route::get('addToCart/{id}','CheckoutController@addToCart');
Route::get('checkout', 'CheckoutController@checkout');
Route::get('/prices', 'CheckoutController@home');
Route::post('/charge', 'CheckoutController@charge')->name('product.payment');;
Route::get('/thankyou', 'PublicController@thankyou');
Route::get('/ordersubmit', 'PublicController@ordersubmit');

Route::post('payment', 'PayPalController@payWithpaypal');

Route::get('forgot-password', 'PublicController@forgot_password');
Route::post('forgotemail', 'PublicController@forgotemails');
Route::get('update_password', 'PublicController@updatepasswords');
Route::post('customer_updatepasword', 'PublicController@customer_updatepaswords');



Route::post('applycoupon', 'CheckoutController@applycoupon');
Route::post('userprofile','CheckoutController@clientprofile');
Route::post('addToCartSubmit', 'CheckoutController@addToCartSubmit');

//Authentication
    Route::post('password/email', 'PasswordController@getemails');
    Route::get('update_password/{slug}', 'PasswordController@usersupdatepasswords');
    Route::post('admin_updatepasword', 'PasswordController@admin_updatepasword');
//Authentication 


//Clear Config cache:
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

Route::prefix('{guard}')->name('guard.')->group(function () {

    Auth::routes(['verify' => true]);
    Route::get('/', 'ResourceController@home');

    Route::resource('/section', 'FrontSectionController');
    Route::post('/section/update', 'FrontSectionController@update');
    Route::post('/section/create', 'FrontSectionController@store');
    
    Route::resource('/testimonials', 'TestimonialsController');
    Route::post('/testimonials/update', 'TestimonialsController@update');
    Route::post('/testimonials/deletetestimonialsrows', 'TestimonialsController@destroy');
    Route::post('/testimonials/deletetestimonials', 'TestimonialsController@deletetestimonials');
    Route::post('/testimonials_orderby', 'TestimonialsController@orderby');
    
    
    Route::resource('/bannersections', 'BannerController');
    Route::post('/bannersections/update', 'BannerController@update');
    Route::post('/bannersections/deletebannersrows','BannerController@destroy');
    Route::post('/bannersections/removefeatures_image','BannerController@removefeatures_image');
 
    Route::post('/bannersections_orderby','BannerController@orderby');    
    
    
    Route::resource('/poster', 'PosterController');
    Route::post('/poster/update', 'PosterController@update');
    Route::post('/poster/deleteposterrows','PosterController@destroy');
    Route::post('/poster/removepostericons','PosterController@removebannericons');
    Route::post('/poster_orderby', 'PosterController@orderby');
    
    
    Route::resource('/taxes', 'TaxController');
    Route::post('/taxes/update', 'TaxController@update');
    Route::post('/taxes/deletetaxesrows','TaxController@destroy');
    Route::post('/taxes/taxes_orderby','TaxController@orderby');
    Route::post('/taxes/process-request','TaxController@process_request');

    
    Route::resource('/services', 'ServicesController');
    Route::post('/services/update', 'ServicesController@update');
    Route::post('/services/deleteservicesrows', 'ServicesController@destroy');
    Route::post('/services/deleteservices', 'ServicesController@deleteservices');
    Route::post('/services_orderby', 'ServicesController@orderby');
    
    
    Route::resource('/coupon', 'CouponController');
    Route::post('/coupon/update', 'CouponController@update');
    Route::post('/coupon/deletecouponrows', 'CouponController@destroy');
    Route::post('/coupon/deleteservices', 'CouponController@deleteservices');
    Route::post('/coupon_orderby', 'CouponController@orderby'); 
    
    
    
    Route::resource('/otherforms','OtherformsController');
    Route::post('/otherforms/update','OtherformsController@update');
    Route::post('/otherforms/otherformsdelete','OtherformsController@otherforms_delete');
    Route::post('/otherforms/deleterows','OtherformsController@destroy');
    Route::post('/otherforms_orderby','OtherformsController@orderby');
    
    
    
    Route::resource('/faqs', 'FaqController');
    Route::post('/faqs/update', 'FaqController@update'); 
    Route::post('/deletefaqsrows', 'FaqController@destroy');
    Route::post('/faq_orderby', 'FaqController@orderby');
    
    
    Route::resource('/faqcategories', 'FaqcategoriesController');
    Route::post('/faqcategories/update', 'FaqcategoriesController@update');
    Route::post('/deletefaqcategoriesrows', 'FaqcategoriesController@destroy');
    Route::post('/faqcategories_orderby', 'FaqcategoriesController@orderby');
    
    
    Route::get('login/{provider}', 'Auth\SocialAuthController@redirectToProvider');
    Route::post('image-cropper/upload','ImageCropperController@upload');

    Route::post('/serviceuser/uploaduser', 'UserController@uploaduserprofile');
    Route::resource('/media', 'LibraryController');
    Route::post('/media/deleteimage', 'LibraryController@deleteimage');
    Route::post('/media/deleteimagemap', 'LibraryController@deleteimagemap');
    Route::post('/media/update', 'LibraryController@update');
    Route::any('/media/getlibrary', 'LibraryController@getlibrary');
    
    

    Route::post('/medialibrary', 'LibraryController@getmedialibrary');
    Route::post('/getselectimagedetails', 'LibraryController@getselectimagedetails');
    Route::post('/store-multi-file-ajax', 'LibraryController@storeMultiFile');
    
    
    Route::resource('/headerscript', 'HeaderscriptController');
    Route::post('/headerscript/update', 'HeaderscriptController@update'); 
    
    Route::resource('/quickform', 'QuickFormController');
    Route::post('/quickform/update', 'QuickFormController@update');
    Route::post('/quickform/deletequickformrows', 'QuickFormController@destroy');
    
    
    Route::resource('/team', 'TeamMemberController');
    Route::post('/team/update', 'TeamMemberController@update');
    Route::post('/team/deleteiconsimage', 'TeamMemberController@deleteiconsimage');
    Route::post('/team/deleteteamrows', 'TeamMemberController@destroy');
    Route::post('/teammember_orderby', 'TeamMemberController@orderby');
    
    
    Route::get('/submission/form/{slug}', 'SubmissionController@form_type'); 
    Route::resource('/submission', 'SubmissionController');
    
    Route::post('/submission/readmail', 'SubmissionController@readmail');
    Route::post('/submission/unreadmail', 'SubmissionController@unreadmail');
    Route::post('/submission/deletemail', 'SubmissionController@deletemail');
    Route::post('/submission/deletesubmissionrows', 'SubmissionController@destroy');
    
    
    Route::resource('/locations', 'LocationController');
    Route::post('/locations/update', 'LocationController@update');
    Route::post('/locations/deletelocations', 'LocationController@deletelocation');
    Route::post('/locations/deletelocationsrows', 'LocationController@destroy');
    Route::post('/locations_orderby', 'LocationController@orderby');
    
    
    Route::resource('/customsettings', 'CustomsettingController');
    Route::post('/customsettings/update', 'CustomsettingController@update');
    Route::post('/customsettings/removefavicons', 'CustomsettingController@removefavicons');
    Route::post('/customsettings/removelogin_logo', 'CustomsettingController@removelogin_logo');
    Route::post('/customsettings/removeadmin_logo', 'CustomsettingController@removeadmin_logo');

    
    Route::resource('/reviews', 'ReviewController');
    Route::post('/reviews/update', 'ReviewController@update');
    Route::post('/reviews/deletebrandsrows', 'ReviewController@destroy');
    Route::post('/reviews/deletebrands', 'ReviewController@deletebrands');
    
    
    
    
    Route::resource('/products', 'ProductsController');
    Route::post('/products/update', 'ProductsController@update');
    Route::post('/products/removefeatured_image', 'ProductsController@removefeatured_image');
    Route::post('/products/deleteproductssrows', 'ProductsController@destroy');
    Route::post('/products/deletegalleryimage', 'ProductsController@deleteimage');
    Route::post('/products/product_orderby', 'ProductsController@orderby'); 
    Route::post('/products/importcsv', 'ProductsController@importcsv'); 
    Route::post('/products/products_galleryorderby', 'ProductsController@products_galleryorderby'); 



    Route::resource('/productcategories', 'ProductcategoriesController');
    Route::post('/productcategories/update', 'ProductcategoriesController@update');
    Route::post('/deleteproductcategoriesrows', 'ProductcategoriesController@destroy');
    Route::post('/productcategories_orderby', 'ProductcategoriesController@orderby');  
    
    
    Route::resource('/blogs', 'BlogController');
    Route::post('/blogs/update', 'BlogController@update'); 
    Route::post('/blogs/deleteblogsrows', 'BlogController@destroy'); 
    Route::post('/blogs/deletefeaturedimage', 'BlogController@deletefeaturedimage'); 
    
    Route::post('/blogs/orderby', 'BlogController@orderby');
    Route::resource('/blogcategories', 'BlogcategoriesController');
    Route::post('/blogcategories/update', 'BlogcategoriesController@update');
    Route::post('/blogcategoriesdelete', 'BlogcategoriesController@destroy');
    Route::post('/blogcategories_orderby', 'BlogcategoriesController@orderby');
    
    

    
    Route::resource('/emailtemplate', 'EmailtemplateController');
    Route::post('/emailtemplate/update', 'EmailtemplateController@update');
    
    
    
    Route::resource('/customer', 'ShopController');
    Route::post('/customer/customerupdate', 'ShopController@customerupdate');
    Route::resource('/woo_settings', 'PaymentsettingController');
    Route::post('/woo_settings/update','PaymentsettingController@update');
    Route::resource('/woocommerce_emails_settings','WooCommerceController');
    Route::post('/woocommerce_emails_settings/update','WooCommerceController@update');
    Route::any('/wooguests', 'ShopController@guest');
    Route::any('/guestedit/{slug}', 'ShopController@guestedit');
    
    Route::post('/wooguests_delete', 'ShopController@wooguests_deletes');
    Route::post('/customer_delete', 'ShopController@customer_deletes');
    Route::get('/shop_order', 'ShopController@orders');
    Route::get('/order_status/{slug}', 'ShopController@order_status');
    Route::post('/order_statuss/update', 'ShopController@update');
    Route::post('/orders_delete', 'ShopController@orders_deletes');
    
    
    
    
    
    
});
Route::any('/{page?}','PublicController@notfound');