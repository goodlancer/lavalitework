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
use App\Whybannerdrugs;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use File;

class PosterController extends BaseController
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
        $data        =  DB::table('whybannerdrugs')->orderBy('order_by', 'asc')->get();
        $menus_title = DB::table('menus')->select('name')->where('slug','poster')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle($menus_title)
            ->view('poster.index')
            ->data(compact('data','menus_title'))
            ->output();
    }
    public function create(Request $request)
    {
          $menus_title = DB::table('menus')->select('name')->where('slug','poster')->first();
          $menus_title = $menus_title->name;
          return $this->response->setMetaTitle($menus_title)
          ->view('poster.create', true)
          ->data(compact('data','menus_title'))
          ->output();
    }

    public function store(Request $request)
    {
       
        try {
            
             $whybannerdrugs                 = Whybannerdrugs::create();
             $whybannerdrugs->title          = $request->title;
             $whybannerdrugs->status         = $request->status;
             $whybannerdrugs->links            = $request->links;

            
            
              $whybannerdrugs->icons                          = $request->icons_image;
            
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
//                    $whybannerdrugs->icons = $filePath; 
//                }
            
              $hover_image                          = $request->hover_image;
         
            
            
            
             $whybannerdrugs->save();
            
             return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
             ->code(204)
             ->status('success')
             ->url(guard_url('poster'))
             ->redirect();
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('poster'))
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
            
        
            $data = DB::table('whybannerdrugs')
               ->where('id',$id)->first();
            $title = $data->title;
       
            return $this->response->setMetaTitle($title)
            ->view('poster.edit')
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
        try{                           
             $whybannerdrugs                  = Whybannerdrugs::find($request->id);
             $whybannerdrugs->title           = $request->title;
             $whybannerdrugs->status          = $request->status;
             $whybannerdrugs->links           = $request->links;
             $whybannerdrugs->icons           = $request->icons_image;
//              if (!empty($image)) {
//
//
//                $folder                     = '/uploads/';
//                $image                      = $request->file('icons_image');
//                $file_slug_name             = $image->getClientOriginalName();
//                $file_slug_name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_slug_name);
//                $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_slug_name_without_ext)));
//                $name                       = $file_slug_name_without_ext.''.time().'.'.$image->getClientOriginalExtension();
//                $destinationPath            = public_path('/storage/uploads/');
//                $filePath                   = $folder . $name;
//
//                $image->move($destinationPath, $name);
//                $whybannerdrugs->icons = $filePath; 
//             }

             $whybannerdrugs->save();
             return redirect()->back();
            
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('poster/'))
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
            $delete = Whybannerdrugs::find($id)->delete();
        }  catch (Exception $e) {
           return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('poster'))
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

    public function removebannericons() 
    {
  
        $imageindex      =  $_POST['imageid'];
        $filepath        =  $_POST['filepath'];
        
 
        $image_path = public_path("/storage".$filepath."");
        //File::delete($image_path);
        $values = array(
            'icons'=>""
        ); 
        DB::table('whybannerdrugs')
        ->where('id',$imageindex)
        ->update($values);
        
  
    }   
    
    public function orderby()
    {
          $sectionid =  $_POST['sectionid'];
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('whybannerdrugs')
            ->where('id',$getkey)
            ->update($values);
         }
    }

    
    
    

    
    
    
    
    

}
