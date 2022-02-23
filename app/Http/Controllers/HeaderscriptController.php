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
use App\Headerscript;
use App\Service;
use App\Faq;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;

class HeaderscriptController extends BaseController
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
        

    	$user         = $request->user()->toArray();
    	$page_setting = Headerscript::all();
        $menus_title = DB::table('menus')->select('name')->where('slug','header-script')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle(__('app.service'))
            ->view('headerscript.index')
            ->data(compact('user','page_setting','menus_title'))
            ->output();
        
    }
    public function create(Request $request)
    {
      $pagestitle = DB::table('pages')->get();
        $menus_title = DB::table('menus')->select('name')->where('slug','header-script')->first();
        $menus_title = $menus_title->name;
      return $this->response->setMetaTitle(trans('app.new') . ' ' . trans('user::headerscript.name'))
      ->view('headerscript.create', true)
      ->data(compact('pagestitle','menus_title'))
      ->output();
    }
    public function store(Request $request)
    {
        try 
        {
            $info      = $request->google_analytics;
            $google_analytics_body      = $request->google_analytics_body;
            $name      = $request->name;
            $values    = array('content' =>  $info,'content2'=>$google_analytics_body, 'name'=>$name );
            DB::table('headerscripts')->insert($values);
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::service.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('headerscript'))
                ->redirect();
                
            } 
            catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('headerscript'))
                ->redirect();
           }
    }
    public function edit($id)
    {
        $pagesetting = Headerscript::find($id);
        return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::faq.name'))
        ->view('headerscript.edit')
        ->data(compact('pagesetting'))
        ->output();
    }
    public function update(Request $request)
    {
  
        try {
             $content = $request->content;
             $google_analytics_body = $request->content2;
             $name = $request->name;
             $values = array('content' => $content,'content2'=>$google_analytics_body,'name'=>$name);
             DB::table('headerscripts')
             ->where('id',  $request->id)
             ->update($values);
            
             return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::headerscript.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('headerscript'))
                ->redirect();
            
          } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('headerscript'))
                ->redirect();
            }

    }
    public function destroy($id)
    {
        
        try {
            
            $googleanalytics = Headerscript::find($id);
            $googleanalytics->delete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::headerscript.name')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('headerscript/'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)     
                ->status('error')
                ->url(guard_url('headerscript'))
                ->redirect();
        }

    }
    public function create_slug($string){
       $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
       return strtolower($slug);
    }

}
