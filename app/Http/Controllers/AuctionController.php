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
use App\Facility;
use App\Auction;
use App\Category;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;

class AuctionController extends BaseController
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
        $user        = $request->user()->toArray();
        $facility    = Category::where('status','publish')->orderBy('order_by','asc')->get();
        $menus_title = DB::table('menus')->select('name')->where('slug','price-plan-payment')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle($menus_title)
        ->view('auction.index')
        ->data(compact('user', 'facility','menus_title'))
        ->output();
    }
    public function show($id)
    {
        $service = Service::find($id);
        return $this->response->setMetaTitle('Categories')
            ->data(compact('service'))
            ->view('service.show')
            ->output();
    }
    public function create(Request $request)
    {
              $menus = DB::table('menus')->where('key', 'category')->first();
              $category = DB::table('menus')->where('parent_id',$menus->id)->get();
        
                $menus_title = DB::table('menus')->select('name')->where('slug','price-plan-payment')->first();
                $menus_title = $menus_title->name;
        
              return $this->response->setMetaTitle($menus_title)
              ->view('auction.create', true)
              ->data(compact('category','menus_title'))
              ->output();
    }
    public function store(Request $request)
    {
      try {

          
            $cnt_categorys = DB::table('categorys')->get();     
            $cntall =  count($cnt_categorys);
          
          
            $name        = $request->name;
            $info        = $request->info;
            $title       = $request->title;
         

            $created_at     = date('Y-m-d H:i:s');
            $description    = $request->description;
            $status         = $request->status;
            $slug           = $this->create_slug($request->name);
            $values         = array(
                                    'name'=>$name,
                                    'title'=>$title,
                                    'info' => $info,
                                    'status' => $status,
                                    'slug'=>$slug,
                                    'order_by'=>$cntall
                                   );
            DB::table('categorys')->insert($values);
          
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
                   ->code(204)
                   ->status('success')
                   ->url(guard_url('categories'))
                   ->redirect();
            
           } 
           catch (Exception $e) {
                return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('categories'))
                ->redirect();
           }

    }
    public function edit($id)
    {
          $facility = Category::find($id);
          return $this->response->setMetaTitle('Categories')
          ->view('auction.edit')
          ->data(compact('facility'))
          ->output();
    }
    public function update(Request $request)
    {

        try {    
                $title          = $request->title;
                $info           = $request->info;
                $name           = $request->name;
                $status         = $request->status;
                $updated_at     = date('Y-m-d H:i:s');
                $values      = array('name'=>$name,'title'=>$title,'info'=>$info,'status'=>$status);
                DB::table('categorys')
                ->where('id',  $request->id)
                ->update($values);
                return redirect()->back();
            } 
            catch (Exception $e) {
                return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('categories/'))
                ->redirect();
            }
    }
    public function destroy($id)
    {
        
        try {
            
            $facility = Category::find($id);
            $facility->delete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::facility.title')]))
                   ->code(202)
                   ->status('success')
                   ->url(guard_url('categorys/'))
                   ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('auction'))
                ->redirect();
        }

    }
    public function create_slug($string){
       $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
       return strtolower($slug);
    }


    
    public function orderby()
    {
         $sectionid =  $_POST['sectionid'];
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('categorys')
            ->where('id',$getkey)
            ->update($values);
         }
    } 

}
