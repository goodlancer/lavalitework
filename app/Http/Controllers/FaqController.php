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
use App\Faqcategories;
use App\Faq;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;

class FaqController extends BaseController
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
    	$user = $request->user()->toArray();
    	$faq = Faq::orderBy('order_by','asc')->get();
        $menus_title = DB::table('menus')->select('name')->where('slug','faqs')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle(__('app.service'))
            ->view('faqs.index')
            ->data(compact('user', 'faq','menus_title'))
            ->output();
    }


    public function create(Request $request)
    {
             
           $allcategories     = Faqcategories::where('status','publish')->get();
        
            $menus_title = DB::table('menus')->select('name')->where('slug','faqs')->first();
            $menus_title = $menus_title->name;
        
            return $this->response->setMetaTitle($menus_title)
            ->view('faqs.create', true)
            ->data(compact('menus_title','allcategories'))
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
            

            $question     = $request->question;
            $answer       = $request->answer;
            $status       = $request->status;
            $category       = $request->category;
            $created_at = date('Y-m-d H:i:s');
            
            
            $values     = array('question' => $question,'answer' => $answer,'created_at'=>$created_at,'status'=>$status,'category'=>$category);
            
            
            DB::table('faqs')->insert($values);
            
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::service.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('faqs'))
                ->redirect();
                
            } 
            catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('faqs'))
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
        
        $faq = Faq::find($id);
        
        $allcategories     = Faqcategories::where('status','publish')->get();
        
        return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::faq.name'))
        ->view('faqs.edit')
        ->data(compact('faq','allcategories'))
        ->output();

    }
    
    public function other(Request $request)
    {
        try {
            $hover_setting = Setting::where('key', 'service.hover.color')->get();
            
            $hover_setting->value = $request->color;
            Log::info($hover_setting);
            $hover_setting->save();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::service.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('service'))
                ->redirect();
            
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('service/'))
                ->redirect();
        }
        
        
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
            
            $question     = $request->question;
            $answer       = $request->answer;
            $status       = $request->status;
            $created_at = date('Y-m-d H:i:s');
            $category       = $request->category;
            
            $values     = array('question' => $question,'answer' => $answer,'created_at'=>$created_at,'status'=>$status,'category'=>$category);
            
            
             DB::table('faqs')
             ->where('id',$request->id)
             ->update($values);
            
            return redirect()->back();
            
          }    catch (Exception $e) 
        {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('faqs/'))
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
            $delete = Faq::find($id)->delete();
        }  catch (Exception $e) {
           return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('faqs'))
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
    public function orderby()
    {
         $sectionid =  $_POST['sectionid'];
         $select    =  DB::table('faqs')->get();
        
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('faqs')
            ->where('id',$getkey)
            ->update($values);
         }
    }

}
