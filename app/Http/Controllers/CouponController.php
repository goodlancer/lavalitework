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

use App\Coupon;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use File;

class CouponController extends BaseController
{
	use RoutesAndGuards, ThemeAndViews, UserPages, UploadTrait;
    /**
     * Initialize public controller.
     *
     * @return null
     */
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
    	$user =  $request->user()->toArray();
        $data =  DB::table('coupons')->orderBy('order_by', 'asc')->get();
        $menus_title = DB::table('menus')->select('name')->where('slug','coupon')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle(__('app.service'))
            ->view('coupon.index')
            ->data(compact('data','menus_title'))
            ->output();
    }

    public function create(Request $request)
    {
        $menus_title = DB::table('menus')->select('name')->where('slug','coupon')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle(trans('app.new') . ' ' . trans('user::service.name'))
         ->view('coupon.create', true)
         ->data(compact('menus_title'))
         ->output();
    }

    /**
     * Create new team.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $coupons                 = Coupon::create();
            $coupons->code           = $request->code;
            $coupons->amount         = $request->amount;
            $coupons->discount_type  = $request->discount_type;
            $coupons->status         = $request->status;
            $coupons->min_amount     = $request->min_amount;
            $coupons->slugs          = $this->createSlug($request->code); 
            $coupons->save();
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
            ->code(204)
            ->status('success')
            ->url(guard_url('coupon'))
            ->redirect();
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('coupon'))
                ->redirect();
        }

    }

    public function edit($id)
    {
            $data = DB::table('coupons')
               ->where('id',$id)->first();
            return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::makediffent.title'))
            ->view('coupon.edit')
            ->data(compact('data'))
            ->output();

    }

    public function update(Request $request)
    {
        
        try {    
            $coupons                  = Coupon::find($request->id);
            $coupons->code           = $request->code;
            $coupons->amount         = $request->amount;
            $coupons->discount_type  = $request->discount_type;
            $coupons->status         = $request->status;
            $coupons->min_amount     = $request->min_amount;
            $coupons->save();
            return redirect()->back();
            
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('coupon/'))
                ->redirect();
        }

    }
    
    
    
    public function createSlug($title, $id = 0)
    {
        $slug = str_slug($title);
        $allSlugs = $this->getRelatedSlugs($slug, $id);
        if (! $allSlugs->contains('slug', $slug)){
            return $slug;
        }

        $i = 1;
        $is_contain = true;
        do {
            $newSlug = $slug . '-' . $i;
            if (!$allSlugs->contains('slug', $newSlug)) {
                $is_contain = false;
                return $newSlug;
            }
            $i++;
        } while ($is_contain);
    }
    protected function getRelatedSlugs($slug, $id = 0)
    {
        return Coupon::select('slugs')->where('slugs', 'like', $slug.'%')
        ->where('id', '<>', $id)
        ->get();
    }
    public function destroy()
    {
       try {
            $id = $_POST['rowid'];
            $delete = Coupon::find($id)->delete();
        }  catch (Exception $e) {
           return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('coupon'))
                ->redirect();
        }  
        
    }
    public function deleteservices()
    {
      
      
      
        $row_id =  $_POST['imageid'];
        $values = array(
            'icons'=>""
        ); 
        DB::table('coupons')
        ->where('id',$row_id)
        ->update($values);   
    }   
    
    public function orderby()
    {
         $sectionid =  $_POST['sectionid'];
         $select    =  DB::table('coupons')->get();
         foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('coupons')
            ->where('id',$getkey)
            ->update($values);
         }
    }

}
