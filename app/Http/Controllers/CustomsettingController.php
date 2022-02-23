<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;

use Litepie\Settings\Http\Requests\SettingRequest;
use Litepie\Settings\Interfaces\SettingRepositoryInterface;

use App\Http\Response\ResourceResponse;
use Litepie\Theme\ThemeAndViews;
use Litepie\User\Traits\RoutesAndGuards;
use Litepie\User\Traits\UserPages;
use Illuminate\Support\Facades\Input;
use App\Traits\UploadTrait;
use App\MySetting;
use App\Service;
use App\Facility;
use App\Customsetting;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use File;


class CustomsettingController extends BaseController
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
        $data =  DB::table('customsettings')->orderBy('id', 'DESC')->get();
        return $this->response->setMetaTitle(__('app.service'))
            ->view('customsettings.index')
            ->data(compact('data'))
            ->output();
    }

    public function create(Request $request)
    {

              return $this->response->setMetaTitle(trans('app.new') . ' ' . trans('user::service.name'))
              ->view('customsettings.create', true)
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
        //Log::info("store");
        try {
            
            $admin_logo  = $request->admin_logo;
            $login_logo  = $request->login_logo;
            $copyright   = $request->copyright;

            $theme_color = $request->theme_color;
            $get_free_quote = $request->get_free_quote;
            
            $filePath1 = '';
            $filePath2 = '';
            
            if (!empty($admin_logo)) {
               
                $folder                     = '/uploads/images/';
                $image                      = $request->file('admin_logo');
                $file_slug_name             = $image->getClientOriginalName();
                $file_slug_name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_slug_name);
                $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_slug_name_without_ext)));
                $name                       = $file_slug_name_without_ext.''.time().'.'.$image->getClientOriginalExtension();
                $destinationPath            = public_path('/storage/uploads/images/');
                $filePath1                   = $folder . $name;
                $image->move($destinationPath, $name);
            }
            
            
             if (!empty($login_logo)) {
               
                $folder                     = '/uploads/images/';
                $image                      = $request->file('login_logo');
                $file_slug_name             = $image->getClientOriginalName();
                $file_slug_name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_slug_name);
                $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_slug_name_without_ext)));
                $name                       = $file_slug_name_without_ext.''.time().''.rand(10,999).'.'.$image->getClientOriginalExtension();
                $destinationPath            = public_path('/storage/uploads/images/');
                $filePath2                  = $folder . $name;
                $image->move($destinationPath, $name);
            }
       
            
            
            $values = array('admin_logo'=>$filePath1,'login_logo'=>$filePath2,'theme_color'=>$theme_color,'copyright'=>$copyright);
            
            
            DB::table('customsettings')->insert($values);
            
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
            ->code(204)
            ->status('success')
            ->url(guard_url('customsettings'))
            ->redirect();
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('customsettings'))
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
                 
            $data = DB::table('customsettings')
               ->where('id',$id)->first();
            return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::customsettings.title'))
            ->view('customsettings.edit')
            ->data(compact('data'))
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
               
  
            $customsetting                 = Customsetting::find($request->id);
            
            $admin_logo     = $request->admin_logo;
            
            $login_logo     = $request->login_logo;
            
            $favicons       = $request->favicons;
            
            $customsetting->copyright      = $request->copyright;
            
            $customsetting->theme_color    = $request->theme_color;
            
            $customsetting->service_area   = $request->service_area;
            
            $customsetting->get_free_quote = $request->get_free_quote;
            
            $customsetting->location       = $request->location;
            $customsetting->custom_css     = $request->custom_css;
            
            $customsetting->shortcodes     = $request->shortcodes;
            
    	     $fileName = 'customize.css';
            
      	     $folder = 'storage/uploads/css/';
     	     File::put($folder.$fileName, $request->custom_css);

            
            if (!empty($admin_logo)) {
               
                $folder                     = '/uploads/images/';
                $image                      = $request->file('admin_logo');
                $file_slug_name             = $image->getClientOriginalName();
                $file_slug_name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_slug_name);
                $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_slug_name_without_ext)));
                $name                       = $file_slug_name_without_ext.''.time().'.'.$image->getClientOriginalExtension();
                $destinationPath            = public_path('/storage/uploads/images/');
                $customsetting->admin_logo                  = $folder . $name;
                $image->move($destinationPath, $name);
            }
            else
            {
                $customsetting->admin_logo = $request->isicons1;
            }
            
             if (!empty($login_logo)) {
               
                $folder                     = '/uploads/images/';
                $image                      = $request->file('login_logo');
                $file_slug_name             = $image->getClientOriginalName();
                $file_slug_name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_slug_name);
                $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_slug_name_without_ext)));
                $name                       = $file_slug_name_without_ext.''.time().''.rand(10,999).'.'.$image->getClientOriginalExtension();
                $destinationPath            = public_path('/storage/uploads/images/');
                $customsetting->login_logo                  = $folder . $name;
                $image->move($destinationPath, $name);
            }
            else
            {
                $customsetting->login_logo = $request->isicons2;
            }
       
            
            
            if (!empty($favicons)) {
               
                $folder                     = '/uploads/images/';
                $image                      = $request->file('favicons');
                $file_slug_name             = $image->getClientOriginalName();
                $file_slug_name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_slug_name);
                $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_slug_name_without_ext)));
                $name                       = $file_slug_name_without_ext.''.time().''.rand(10,999).'.'.$image->getClientOriginalExtension();
                $destinationPath            = public_path('/storage/uploads/images/');
                $customsetting->favicons                  = $folder . $name;
                $image->move($destinationPath, $name);
            }
            else
            {
                $customsetting->favicons = $request->isicons3;
            }  
            
            
            
            $customsetting->save();
            
//            $values = array('admin_logo'=>$filePath1,'login_logo'=>$filePath2,'theme_color'=>$theme_color,'copyright'=>$copyright,'service_area'=>$service_area,'get_free_quote'=>$get_free_quote,'location'=>$location,'favicons'=>$filePath3);
//            
//            DB::table('customsettings')
//            ->where('id',  $request->id)
//            ->update($values);
                
            return redirect()->back();
            
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('customsettings/'))
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
    public function destroy($id)
    {
            
        try {
            
            DB::delete('delete from customsettings where id = ?',[$id]);
            
           
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::success')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('customsettings/'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('customsettings'))
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


    public function removefavicons()
    {
        $filepath   = $_POST['imagepath'];
        $image_path = public_path("/storage".$filepath."");
        File::delete($image_path);
        $row_id     =  $_POST['rowid'];
        $values = array(
            'favicons'=>""
        ); 
        DB::table('customsettings')
        ->where('id',$row_id)
        ->update($values);   
    }
    
    public function removelogin_logo()
    {
        $filepath   = $_POST['imagepath'];
        $image_path = public_path("/storage".$filepath."");
        File::delete($image_path);
        $row_id     =  $_POST['rowid'];
        $values = array(
            'login_logo'=>""
        ); 
        DB::table('customsettings')
        ->where('id',$row_id)
        ->update($values);   
    } 
    
    public function removeadmin_logo()
    {
        
        $filepath   = $_POST['imagepath'];
        
        $image_path = public_path("storage".$filepath."");
        
        File::delete($image_path);
        
        $row_id     =  $_POST['rowid'];
        
        $values = array(
            'admin_logo'=>""
        ); 
        DB::table('customsettings')
        ->where('id',$row_id)
        ->update($values); 
        
    } 
    

    
    
    
    
    

}
