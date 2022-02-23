<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Productcategories;

class AppServiceProvider extends ServiceProvider
{
    
    /**
     * Register any application services.
     *
     * @return void
     */
    
    
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    error_reporting(0);

    if(!isset($_COOKIE['myuserid_2'])) {
          setcookie("myuserid_2", uniqid() , time()+60*60*24); 
    }
     view()->composer('*', function($view) {
         
        if (Auth::check()) {

            $view->with('cartitems', DB::table('carts')->where('user_id',auth()->user()->id)->get());
        } 
        else
        {
             $view->with('cartitems', DB::table('sessioncarts')->where('user_id', $_COOKIE['myuserid_2'])->get());
        }  
         
     });
   
    $googleanalytics          = DB::table('headerscripts')->get();
    $contacts                 = DB::table('contacts')->get();
    $customsettings           = DB::table('customsettings')->get();
    $faqs_public              = DB::table('faqs')->get();
        
    $allservices              = DB::table('services')->where('status','Published')->get();
    $alllocations             = DB::table('locations')->where('status','Published')->get();

    $footersettings           = DB::table('footersettings')->get();

    $total_submission         = DB::table('submissions')->where('status','active')->where('slug','contact-us')->get(); 
    $total_submission_contact_form_2         = DB::table('submissions')->where('status','active')->where('slug','contact-us---contact-page')->get(); 
        
     $total_submission_all_forms         = DB::table('submissions')->where('status','active')->get(); 
        
    $pagess                   = DB::table('pages')->get();
    $users                    = DB::table('users')->get();

    $quote_form_notify        = DB::table('submissions')->where('slug',"contact-us")->where('status',"active")->get();
    $product_cat_global       = Productcategories::where('status','published')->get();
    

      $values = array(
        'googleanalytics'     =>$googleanalytics,
        'contacts'            =>$contacts,
        'customsettings'      =>$customsettings,
        'footersettings'      =>$footersettings,
        'total_submission'    =>$total_submission,
        'submissions'         =>$total_submission,
        'total_submission_all_forms'=>$total_submission_all_forms,
        'contact_us_cnt'      =>$total_submission_contact_form_2,
        'pages'               =>$pagess,
        'users'               =>$users,
        'quote_form_notify'   =>$quote_form_notify,
        'allservices'=>$allservices,
        'alllocations'=>$alllocations,
        'product_cat_global'=>$product_cat_global,
        'faqs_public'=>$faqs_public,
      );
      View::share($values);  
        
    }
}
