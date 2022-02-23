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
use App\Testimonial;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use File;

class TestimonialsController extends BaseController
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
        $data =  DB::table('testimonials')->orderBy('order_by', 'asc')->get();
        
        $menus_title = DB::table('menus')->select('name')->where('slug','testimonials')->first();
        $menus_title = $menus_title->name;
        
        return $this->response->setMetaTitle(__('app.service'))
            ->view('testimonials.index')
            ->data(compact('data','menus_title'))
            ->output();
    }
    public function show($id)
    {
        $service = Service::find($id);
        return $this->response->setMetaTitle(trans('app.view') . ' ' . trans('user::service.name'))
            ->data(compact('service'))
            ->view('testimonials.show')
            ->output();
    }
    public function create(Request $request)
    {
        
     
          $menus_title = DB::table('menus')->select('name')->where('slug','testimonials')->first();
          $menus_title = $menus_title->name;

          return $this->response->setMetaTitle(trans('app.new') . ' ' . trans('user::service.name'))
          ->view('testimonials.create', true)
          ->data(compact('menus_title'))
          ->output();
    }
    public function store(Request $request)
    {
        try {
            
            
            $title = $request->title;
            $filePath = $request->image;
            $description = $request->description;
            $status = $request->status;
            
            
//            $filePath = '';
//            
//            
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
       
            $values = array('title' =>$title,'icons'=>$filePath,'status'=>$status,'description'=>$description);
            DB::table('testimonials')->insert($values);
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
            ->code(204)
            ->status('success')
            ->url(guard_url('testimonials'))
            ->redirect();
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('testimonials'))
                ->redirect();
        }

    }
    public function edit($id)
    {
                 
            $data = DB::table('testimonials')
               ->where('id',$id)->first();
            return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::makediffent.title'))
            ->view('testimonials.edit')
            ->data(compact('data'))
            ->output();

    }
    public function update(Request $request)
    {

        try {                           
               
                $title       = $request->title;
                $filePath       = $request->image;
                $description = $request->description;
                $status      = $request->status;
            
            
//                $filePath='';
//                if (!empty($image)) {
//                    
//                    $folder                     = '/uploads/images/';
//                    $image                      = $request->file('image');
//                    $filename                   = $image->getClientOriginalName();
//                    $file_without_ext           = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
//                    $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_without_ext)));
//                    $name                       = $file_slug_name_without_ext.''.time().'.'.$image->getClientOriginalExtension();
//                    $destinationPath            = public_path('/storage/uploads/images/');
//                    $filePath                   = $folder . $name;
//                    $image->move($destinationPath, $name);
//                   
//                }
//                else
//                {
//                   $filePath=$request->isicons;
//                }

                $values = array('title'=>$title,'icons'=>$filePath,'status'=>$status,'description'=>$description);
                DB::table('testimonials')
                ->where('id',  $request->id)
                ->update($values);
                
                return redirect()->back();

                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('testimonials/'))
                ->redirect();
        }

    }
    public function destroy()
    {    
        try {
            $id = $_POST['rowid'];
            $delete = Testimonial::find($id)->delete();
        }  catch (Exception $e) {
           return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('testimonials'))
                ->redirect();
        }
    }
    public function create_slug($string){
       $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
       return strtolower($slug);
    }
    public function deletetestimonials()
    {
        
        $row_id     = $_POST['imageid'];
        
        $values = array(
            'icons'=>""
        ); 
        DB::table('testimonials')
        ->where('id',$row_id)
        ->update($values);
        
        
    }
    public function orderby()
    {
         $sectionid =  $_POST['sectionid'];
         $select    =  DB::table('testimonials')->get();
        
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('testimonials')
            ->where('id',$getkey)
            ->update($values);
         }
    } 

}
