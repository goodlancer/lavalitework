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
use App\Services;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use File;

class ServicesController extends BaseController
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
        $data =  DB::table('services')->orderBy('order_by', 'asc')->get();
        $menus_title = DB::table('menus')->select('name')->where('slug','services-3')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle(__('app.service'))
            ->view('services.index')
            ->data(compact('data','menus_title'))
            ->output();
    }

    public function create(Request $request)
    {
        $menus_title = DB::table('menus')->select('name')->where('slug','services-3')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle(trans('app.new') . ' ' . trans('user::service.name'))
         ->view('services.create', true)
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
            
            $services                 = Services::create();
            $services->status         = $request->status;
            $services->url            = $request->url;
            $services->title          = $request->title;
            $services->icons          = $request->image;
            $slug  = $this->createSlug($request->title);  
            $services->slug            = $slug;
            $services->save();
            
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
            ->code(204)
            ->status('success')
            ->url(guard_url('services'))
            ->redirect();
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('services'))
                ->redirect();
        }

    }

    public function edit($id)
    {
                 
            $data = DB::table('services')
               ->where('id',$id)->first();

            return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::makediffent.title'))
            ->view('services.edit')
            ->data(compact('data'))
            ->output();

    }

    public function update(Request $request)
    {

        try {                           
               
            $existed_slug       = Services::select('slug')->where('id',$request->id)->first()['slug'];
        
     
            $services                 = Services::find($request->id);
            $services->status         = $request->status;
            $services->url            = $request->url;
            $services->title          = $request->title;
            $services->icons          = $request->image;
            if($slugs)
            {
                   $slug  = $this->createSlug($request->slug);  
            }
            else
            {
                   $slug  = $this->createSlug($request->title);  
            }
            $services->slug            = $request->slug;
            if($existed_slug == $current_slug)
            {
                $slug = $current_slug;
            }
            else
            {
                 $slug = $this->createSlug($current_slug);
            }
            
            $filePath           = $request->image;
            $services->save();
//            $filePath = '';
//            if (!empty($image)) {
//               
//                $folder                     = '/uploads/images/';
//                $image                      = $request->file('image');
//                $file_slug_name             = $image->getClientOriginalName();
//                $file_slug_name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_slug_name);
//                $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_slug_name_without_ext)));
//                $name                       = $file_slug_name_without_ext.''.time().'.'.$image->getClientOriginalExtension();
//                $destinationPath            = public_path('/storage/uploads/images/');
//                $filePath                   = $folder . $name;
//                $image->move($destinationPath, $name);
//            }
//            else
//            {
//                $filePath = $request->isicons;
//            }
            

            return redirect()->back();      
            
            
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('services/'))
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
        return Services::select('slug')->where('slug', 'like', $slug.'%')
        ->where('id', '<>', $id)
        ->get();
    }
    public function destroy()
    {
       try {
            $id = $_POST['rowid'];
            $delete = Services::find($id)->delete();
        }  catch (Exception $e) {
           return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('services'))
                ->redirect();
        }  
        
    }
    public function deleteservices()
    {
      
      
      
        $row_id =  $_POST['imageid'];
        $values = array(
            'icons'=>""
        ); 
        DB::table('services')
        ->where('id',$row_id)
        ->update($values);   
    }   
    
    public function orderby()
    {
         $sectionid =  $_POST['sectionid'];
         $select    =  DB::table('services')->get();
         foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('services')
            ->where('id',$getkey)
            ->update($values);
         }
    }

}
