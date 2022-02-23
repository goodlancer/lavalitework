<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Response\ResourceResponse;
use Litepie\Theme\ThemeAndViews;
use Litepie\User\Traits\RoutesAndGuards;
use Litepie\User\Traits\UserPages;
use Illuminate\Support\Facades\Input;
use App\Traits\UploadTrait;
use App\MySetting;
use App\Service;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use File;
use App\Paymentsetting;

class PaymentsettingController extends BaseController
{
	use RoutesAndGuards, ThemeAndViews, UserPages, UploadTrait;
    public function __construct()
    {
        Log::info('helo');
        guard(request()->guard . '.web');
        $this->middleware('auth:' . guard());
        $this->middleware('verified:guard.verification.notice');
        $this->middleware('role:' . $this->getGuardRoute());
        $this->response = app(ResourceResponse::class);
        $this->setTheme();
    }
    public function index(Request $request)
    {
        
    	$paymentsettings = Paymentsetting::all();
        
        $menus_title = DB::table('menus')->select('name')->where('slug','settings')->first();
        $menus_title = $menus_title->name;
        
        
        return $this->response->setMetaTitle($menus_title)
            ->view('paymentsettings.index')
            ->data(compact('paymentsettings','menus_title'))
            ->output();
    }
    public function show($id)
    {
        $service = Paymentsetting::find($id);
        return $this->response->setMetaTitle(trans('app.view') . ' ' . trans('user::service.name'))
            ->data(compact('service'))
            ->view('service.show')
            ->output();
    }
    public function create(Request $request)
    {
              $menus = DB::table('menus')->where('key', 'category')->first();
              return $this->response->setMetaTitle("New Payment")
              ->view('paymentsettings.create', true)
              ->data(compact('category'))
              ->output();
    }
    public function store(Request $request)
    {
        Log::info("store");
        try {
            $paymentsetting         = Paymentsetting::create();
            $paymentsetting->status = $request->status;
            $paymentsetting->save();
            
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::service.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('woo_settings'))
                ->redirect();
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('woo_settings'))
                ->redirect();
        }

    }

    public function edit($id)
    {
        $data = Paymentsetting::find($id);
        if($data->slug == "paypal")
        {
              return $this->response->setMetaTitle("PayPal Settings")
                ->view('paymentsettings.paypaledit')
                ->data(compact('data'))
                ->output(); 
        }
        
        if($data->slug == "stripe")
        {
         return $this->response->setMetaTitle("PayPal Settings")
          ->view('paymentsettings.stripeedit')
          ->data(compact('data'))
          ->output(); 
        }

    }
    
    
    public function update(Request $request)
    {

        Log::info("update");
        try {
             $paymentsetting               = Paymentsetting::find($request->id);
             $paymentsetting->status       = $request->status;
             $paymentsetting->title        = $request->title;
             $paymentsetting->environment  = $request->environment;
             $paymentsetting->descriptions = $request->descriptions;
             //Live
             $paymentsetting->live_api_username  = $request->live_api_username;
             $paymentsetting->live_api_password  = $request->live_api_password;
             $paymentsetting->live_api_signature = $request->live_api_signature;
             //Sandbox
             $paymentsetting->sandbox_api_username  = $request->sandbox_api_username;
             $paymentsetting->sandbox_api_password  = $request->sandbox_api_password;
             $paymentsetting->sandbox_api_signature = $request->sandbox_api_signature;
            
            
          
             $paymentsetting->save();
             return redirect()->back();
          
        } 
        catch (Exception $e) {
          
        }

    }
    public function destroy($id)
    {
        
        try {
            
            $service = Service::find($id);
            $service->delete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::service.name')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('service/'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('service'))
                ->redirect();
        }

    } 

    public function create_slug($string){
       $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
       return strtolower($slug);
    }


}
