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
use App\Building;
use App\Otherforms;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use File;

class OtherformsController extends BaseController
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
        $data =  Otherforms::orderBy('order_by', 'asc')->get();
        $menus_title = DB::table('menus')->select('name')->where('slug','other-form')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle(__('app.service'))
            ->view('otherforms.index')
            ->data(compact('data','menus_title'))
            ->output();
    }
    public function create(Request $request)
    {
            $menus_title = DB::table('menus')->select('name')->where('slug','other-form')->first();
            $menus_title = $menus_title->name;
              return $this->response->setMetaTitle(trans('app.new') . ' ' . trans('user::service.name'))
              ->view('otherforms.create', true)
              ->data(compact('menus_title'))
              ->output();
    }
    public function store(Request $request)
    {
        try {
            
            $otherforms              = Otherforms::create();
            $otherforms->title       = $request->title;
            $otherforms->formurl     = $request->formurl;
            $image                   = $request->icons;
            $otherforms->description = $request->description;
            $otherforms->status      = $request->status;
            $filePath = '';
            if (!empty($image)) {
                $folder                     = '/uploads/';
                $image                      = $request->file('icons');
                $file_slug_name             = $image->getClientOriginalName();
                $file_slug_name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_slug_name);
                $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_slug_name_without_ext)));
                $name                       = $file_slug_name_without_ext.''.time().'.'.$image->getClientOriginalExtension();
                $destinationPath            = public_path('/storage/uploads/');
                $filePath                   = $folder . $name;
                $otherforms->icons            = $filePath;
                $image->move($destinationPath, $name);
            }
            $otherforms->save();
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
            ->code(204)
            ->status('success')
            ->url(guard_url('otherforms'))
            ->redirect();
            
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('otherforms'))
                ->redirect();
        }

    }
    public function edit($id)
    {
                 
            $data = Otherforms::where('id',$id)->first();

            return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::makediffent.title'))
            ->view('otherforms.edit')
            ->data(compact('data'))
            ->output();

    }
    public function update(Request $request)
    {
        try {                           
                $otherforms              = Otherforms::find($request->id);
                $otherforms->title       = $request->title;
                $image                   = $request->icons;
                $otherforms->description = $request->description;
                $otherforms->status      = $request->status;
            
                $otherforms->formurl     = $request->formurl;
            
                $filePath='';
                if (!empty($image)) {
                    
                    $folder                     = '/uploads/';
                    $image                      = $request->file('icons');
                    $filename                   = $image->getClientOriginalName();
                    $file_without_ext           = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
                    $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_without_ext)));
                    $name                       = $file_slug_name_without_ext.''.time().'.'.$image->getClientOriginalExtension();
                    $destinationPath            = public_path('/storage/uploads/');
                    $filePath                   = $folder . $name;
                    
                    $otherforms->icons      = $filePath;
                    $image->move($destinationPath, $name);
                   
                }
                else
                {
                    $otherforms->icons =$request->isicons;
                }

                $otherforms->save();
                
                return redirect()->back();

                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('otherforms/'))
                ->redirect();
        }

    }
    public function destroy()
    {
            

       try {
            $id = $_POST['rowid'];
            $delete = Otherforms::find($id)->delete();
        }  catch (Exception $e) {
           return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('services'))
                ->redirect();
        }  

    }
    public function create_slug($string){
       $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
       return strtolower($slug);
    }
    public function otherforms_delete()
    {

        $filepath   = $_POST['filepath'];

        $image_path = public_path("/storage".$filepath."");
        File::delete($image_path);
        $row_id =  $_POST['imageid'];
        
        $values = array(
            'icons'=>""
        ); 
        DB::table('otherforms')
        ->where('id',$row_id)
        ->update($values);   
    }
    public function orderby()
    {
         $sectionid =  $_POST['sectionid'];
         $select    =  DB::table('otherforms')->get();
        
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('otherforms')
            ->where('id',$getkey)
            ->update($values);
         }
    }
    
    

    
    
    
    
    

}
