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
use App\Paymentsetting;
use App\Wc_email_invoice;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;

class WooCommerceController extends BaseController
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
        $id =1;
        $data = Wc_email_invoice::find($id);
        $paymentsettings    = Paymentsetting::all();
        return $this->response->setMetaTitle(__('Shop:Settings'))
            ->view('shopsetting.email_orders')
            ->data(compact('data'))
            ->output();
    }

    

    /**
     * Display team.
     *
     * @param Request $request
     * @param Model   $team
     *
     * @return Response
     */


    /**
     * Show the form for creating a new team.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request)
    {
              $menus = DB::table('menus')->where('key', 'category')->first();
             
        
              $category = Category::where('status','publish')->get();
              return $this->response->setMetaTitle(trans('app.new') . ' ' . trans('user::service.name'))
              ->view('facility.create', true)
              ->data(compact('category'))
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
        Log::info("store");


        try {
            $wc_email_invoice                     = Wc_email_invoice::create();
            $wc_email_invoice->subject            = $request->subject;
            $wc_email_invoice->email_heading      = $request->email_heading;
            $wc_email_invoice->content            = $request->content;
            $wc_email_invoice->from_email         = $request->from_email;
            $wc_email_invoice->admin_email        = $request->admin_email;
            $wc_email_invoice->save();
            
             return redirect()->back();     
            
            } 
             catch (Exception $e) {
               return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('facility'))
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
           $facility = Auction::find($id);
        	$category = Category::where('status','publish')->get();
            return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::facility.name'))
            ->view('facility.edit')
            ->data(compact('facility','category'))
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

        
  
        Log::info("update");
        try {
             $wc_email_invoice                = Wc_email_invoice::find($request->id);
             $wc_email_invoice->subject       = $request->subject;
             $wc_email_invoice->email_heading      = $request->email_heading;
             $wc_email_invoice->content            = $request->content;
             $wc_email_invoice->from_email         = $request->from_email;
             $wc_email_invoice->admin_email        = $request->admin_email;
            
             $wc_email_invoice->acc_subject              = $request->acc_subject;
             $wc_email_invoice->acc_email_heading        = $request->acc_email_heading;
             $wc_email_invoice->acc_from_email           = $request->acc_from_email;
             $wc_email_invoice->acc_content              = $request->acc_content;
            
            
             $wc_email_invoice->save();
             return redirect()->back();
          
        } 
        catch (Exception $e) {
          
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
            
            $facility = Facility::find($id);
            $facility->delete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::facility.title')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('facility/'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('facility'))
                ->redirect();
        }

    }

    /**
     * Restore deleted teams.
     *
     * @param Model   $team
     *
     * @return Response
     */
    public function restore(Request $request)
    {
        try {
            $ids = hashids_decode($request->input('ids'));
            $this->repository->restore($ids);

            return $this->response->message(trans('messages.success.restore', ['Module' => trans('user::team.name')]))
                ->status("success")
                ->code(202)
                ->url(guard_url('/teams/team'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->status("error")
                ->code(400)
                ->url(guard_url('/user/team/'))
                ->redirect();
        }

    }

    /**
     * Attach a user to a team.
     *
     * @param Request $request
     * @param Model   $team
     *
     * @return Response
     */
    public function attach(Request $request)
    {
        try {
            $attributes = $request->all();

            $team = $this->repository->attach($attributes);
            return $this->response->message(trans('messages.success.attached', ['Module' => trans('user::team.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('user/team/' . $team->getRouteKey()))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('user/team/' . $team->getRouteKey()))
                ->redirect();
        }

    }
    /**
     * Detach a user from a team.
     *
     * @param Request $request
     * @param Model   $team
     *
     * @return Response
     */
    public function detach(Request $request)
    {
        try {
            $attributes = $request->all();
            $team = $this->repository->detach($attributes);
            return $this->response->message(trans('messages.success.detached', ['Module' => trans('user::team.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('user/team/' . $team->getRouteKey()))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('user/team/' . $team->getRouteKey()))
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

    public function deletefacilityimage() 
    {


         $imageindex =  $_POST['imageid'];
         $currentservices =  DB::table('facility')
         ->where('id',  $imageindex)
         ->first();
       //  echo '<pre>',var_dump($currentservices); echo '</pre>';
        
            $values = array(
                'image'=>""
            ); 
        
            DB::table('facility')
            ->where('id',$imageindex)
            ->update($values);
    }
    
    public function deleteiconsimage() 
    {


         $imageindex      =  $_POST['imageid'];
         $currentservices =  DB::table('facility')
         ->where('id',  $imageindex)
         ->first();
       //  echo '<pre>',var_dump($currentservices); echo '</pre>';
        
            $values = array(
                'icons'=>""
            ); 
        
            DB::table('facility')
            ->where('id',$imageindex)
            ->update($values);
    }
    public function deletetailor()
    {

        $dataid        =  $_POST['dataid'];


        DB::table('auctions')
        ->where('id',$dataid)
        ->delete();

    }

}
