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
use App\Bannersection;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use File;

class BannerController extends BaseController
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
    	$user        =  $request->user()->toArray();
        $data        =  DB::table('bannersections')->orderBy('order_by', 'asc')->get();
        $menus_title = DB::table('menus')->select('name')->where('slug','banner-section-home')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle($menus_title)
            ->view('bannersections.index')
            ->data(compact('data','menus_title'))
            ->output();
    }
    public function create(Request $request)
    {
          $menus_title = DB::table('menus')->select('name')->where('slug','banner-section-home')->first();
          $menus_title = $menus_title->name;
          return $this->response->setMetaTitle($menus_title)
          ->view('bannersections.create', true)
          ->data(compact('data','menus_title'))
          ->output();
    }

    public function store(Request $request)
    {
       
       
        
        try {
            
             $bannersections                 = Bannersection::create();
             $bannersections->heading        = $request->heading;
             $bannersections->name           = $request->name;
             $bannersections->status         = $request->status;
             $bannersections->url            = $request->url;
             $bannersections->content        = $request->content;
             $bannersections->icons          = $request->icons;
            
             $bannersections->icons_image                      = $request->icons_image;
            
            
            
            
//              $image                          = $request->icons_image;
//              if (!empty($image)) {
//                    $folder                     = '/uploads/';
//                    $image                      = $request->file('icons_image');
//                    $file_slug_name             = $image->getClientOriginalName();
//                    $file_slug_name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_slug_name);
//                    $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_slug_name_without_ext)));
//                    $name                       = $file_slug_name_without_ext.''.time().'.'.$image->getClientOriginalExtension();
//                    $destinationPath            = public_path('/storage/uploads/');
//                    $filePath                   = $folder . $name;
//                    $image->move($destinationPath, $name);
//                    $bannersections->icons_image = $filePath; 
//                }
//            
//              $hover_image                          = $request->hover_image;
//              if (!empty($hover_image)) {
//                  
//           
//                    $folder                     = '/uploads/';
//                    $image                      = $request->file('hover_image');
//                    $file_slug_name             = $image->getClientOriginalName();
//                    $file_slug_name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_slug_name);
//                    $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_slug_name_without_ext)));
//                    $name                       = $file_slug_name_without_ext.''.time().'.'.$image->getClientOriginalExtension();
//                    $destinationPath            = public_path('/storage/uploads/');
//                    $filePath                   = $folder . $name;
//           
//                    $image->move($destinationPath, $name);
//                    $bannersections->hover_image = $filePath; 
//                }
//            
//            
//              $background_image                          = $request->background_image;
//              if (!empty($background_image)) {
//                  
//           
//                    $folder                     = '/uploads/';
//                    $image                      = $request->file('background_image');
//                    $file_slug_name             = $image->getClientOriginalName();
//                    $file_slug_name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_slug_name);
//                    $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_slug_name_without_ext)));
//                    $name                       = $file_slug_name_without_ext.''.time().'.'.$image->getClientOriginalExtension();
//                    $destinationPath            = public_path('/storage/uploads/');
//                    $filePath                   = $folder . $name;
//           
//                    $image->move($destinationPath, $name);
//                    $bannersections->background_image = $filePath; 
//                }
            
            
            
             $bannersections->save();
            
             return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
             ->code(204)
             ->status('success')
             ->url(guard_url('bannersections'))
             ->redirect();
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('bannersections'))
                ->redirect();
        }

    }

    /**
     * Show team for editing.
     *
     * @param Request $request
     * @param Model   $team
     *
     * @return Response
     */
    public function edit($id)
    {
            
        
            $data = DB::table('bannersections')
               ->where('id',$id)->first();
            $title = $data->name;
       
            return $this->response->setMetaTitle($title)
            ->view('bannersections.edit')
            ->data(compact('data','title'))
            ->output();

    }
    
    /**
     * Update the team.
     *
     * @param Request $request
     * @param Model   $team
     *
     * @return Response
     */
    public function update(Request $request)
    {

        try {                           
               
             $bannersections                 = Bannersection::find($request->id);
             $bannersections->heading        = $request->heading;
             $bannersections->name           = $request->name;
             $bannersections->status         = $request->status;
             $bannersections->url            = $request->url;
             $bannersections->content        = $request->content;
             $bannersections->icons          = $request->icons;
            
             $bannersections->icons_image                      = $request->icons_image;
            
            
//              if (!empty($image)) {
//                  
//           
//                    $folder                     = '/uploads/';
//                    $image                      = $request->file('icons_image');
//                    $file_slug_name             = $image->getClientOriginalName();
//                    $file_slug_name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_slug_name);
//                    $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_slug_name_without_ext)));
//                    $name                       = $file_slug_name_without_ext.''.time().'.'.$image->getClientOriginalExtension();
//                    $destinationPath            = public_path('/storage/uploads/');
//                    $filePath                   = $folder . $name;
//           
//                    $image->move($destinationPath, $name);
//                    $bannersections->icons_image = $filePath; 
//                }
//            
            
             // $bannersections->hover_image  = $request->hover_image;
//              if (!empty($hover_image)) {
//                  
//           
//                    $folder                     = '/uploads/';
//                    $image                      = $request->file('hover_image');
//                    $file_slug_name             = $image->getClientOriginalName();
//                    $file_slug_name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_slug_name);
//                    $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_slug_name_without_ext)));
//                    $name                       = $file_slug_name_without_ext.''.time().'.'.$image->getClientOriginalExtension();
//                    $destinationPath            = public_path('/storage/uploads/');
//                    $filePath                   = $folder . $name;
//           
//                    $image->move($destinationPath, $name);
//                    $bannersections->hover_image = $filePath; 
//                }
//            
            
              // $bannersections->background_image   = $request->background_image;
            
            
            
//              if (!empty($background_image)) {
//                  
//           
//                    $folder                     = '/uploads/';
//                    $image                      = $request->file('background_image');
//                    $file_slug_name             = $image->getClientOriginalName();
//                    $file_slug_name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_slug_name);
//                    $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_slug_name_without_ext)));
//                    $name                       = $file_slug_name_without_ext.''.time().'.'.$image->getClientOriginalExtension();
//                    $destinationPath            = public_path('/storage/uploads/');
//                    $filePath                   = $folder . $name;
//           
//                    $image->move($destinationPath, $name);
//                    $bannersections->background_image = $filePath; 
//                }
            
            
             $bannersections->save();
            
             return redirect()->back();
            
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('bannersections/'))
                ->redirect();
        }

    }
    /**
     * Remove the team.
     *
     * @param Model   $team
     *
     * @return Response
     */
    public function destroy()
    {
            

       try {
            $id = $_POST['rowid'];
            $delete = Bannersection::find($id)->delete();
        }  catch (Exception $e) {
           return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('bannersections'))
                ->redirect();
        }  

    }


  
    /**
     * Create slug for title of service
    */
    public function create_slug($string){
       $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
       return strtolower($slug);
    }

    public function removefeatures_image() 
    {
  
        $imageindex      =  $_POST['imageid'];

        $values = array(
            'icons_image'=>""
        ); 
        DB::table('bannersections')
        ->where('id',$imageindex)
        ->update($values);
        
  
    }   
    
    public function removebannerhovericons() 
    {
  
        $imageindex      =  $_POST['imageid'];
        $values = array(
            'hover_image'=>""
        ); 
        DB::table('bannersections')
        ->where('id',$imageindex)
        ->update($values);
        
  
    }    
    
    public function removebannerbackground() 
    {
  
        $imageindex      =  $_POST['imageid'];
        $filepath        =  $_POST['filepath'];
        $values = array(
            'background_image'=>""
        ); 
        DB::table('bannersections')
        ->where('id',$imageindex)
        ->update($values);
        
  
    }
    public function removebannerhomepage_1()
    {
        //echo "test 1";
        
        
        
        $filepath   = $_POST['filepath'];
        $image_path = public_path("/storage".$filepath."");
        File::delete($image_path);
        $row_id =  $_POST['imageid'];
        $values = array(
            'background_image'=>""
        ); 
        DB::table('banners')
        ->where('id',$row_id)
        ->update($values);   
    }
    
    public function removebannerhomepage_2()
    {

        $filepath   = $_POST['filepath'];
        $image_path = public_path("/storage".$filepath."");
        File::delete($image_path);
        $row_id =  $_POST['imageid'];
        $values = array(
            'image'=>""
        ); 
        DB::table('banners')
        ->where('id',$row_id)
        ->update($values);   
    }
     
    public function orderby()
    {
          $sectionid =  $_POST['sectionid'];
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('bannersections')
            ->where('id',$getkey)
            ->update($values);
         }
    }
 

}
