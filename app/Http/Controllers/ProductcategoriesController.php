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
use App\Auction;
use App\Productcategories;
use App\Category;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;

class ProductcategoriesController extends BaseController
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
    	$allcategories     = Productcategories::orderBy('order_by','asc')->get();
        
        $menus_title       = DB::table('menus')->select('name')->where('slug','product-categories')->first();
        $menus_title       = $menus_title->name;
        
        return $this->response->setMetaTitle($menus_title)  
                ->view('productcategories.index')
                ->data(compact('menus_title','allcategories'))
                ->output();
        
    }

    public function create(Request $request)
    {

      $menus_title = DB::table('menus')->select('name')->where('slug','product-categories')->first();
      $menus_title = $menus_title->name;
      $category = Productcategories::all();
      return $this->response->setMetaTitle($menus_title)
      ->view('productcategories.create', true)
      ->data(compact('category','menus_title'))
      ->output();

        
    }

    public function store(Request $request)
    {

      try {

              $productcategories               = Productcategories::create();
              $productcategories->subtitle      = $request->subtitle;
              $productcategories->image         = $request->image;
              $productcategories->title         = $request->title;
              $productcategories->descriptions         = $request->descriptions;
              $productcategories->slug         = $this->createSlug($request->title);
              $productcategories->status       = $request->status;
              $productcategories->home_features         = $request->home_features;
              $productcategories->save();

              return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
                   ->code(204)
                   ->status('success')
                   ->url(guard_url('productcategories'))
                   ->redirect();
            
           } 
           catch (Exception $e) {
                return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('productcategories'))
                ->redirect();
           }

    }

    public function edit($id)
    {
          $data = Productcategories::find($id);
          return $this->response->setMetaTitle('Categories')
          ->view('productcategories.edit')
          ->data(compact('data'))
          ->output();
    }

    public function update(Request $request)
    {

        try {    
                $productcategories                = Productcategories::find($request->id);
                $productcategories->title         = $request->title;
                $productcategories->subtitle      = $request->subtitle;
                $productcategories->image         = $request->image;
                
                $productcategories->descriptions         = $request->descriptions;
                $productcategories->home_features         = $request->home_features;
                
                 
                $productcategories->status        = $request->status;
                $productcategories->slug          = $this->createSlug($request->title);
                $productcategories->save();  
                return redirect()->back();
            } 
            catch (Exception $e) {
                return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('productcategories/'))
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
            $delete = Productcategories::find($id)->delete();
        }  catch (Exception $e) {
           return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('productcategories'))
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
        return Productcategories::select('slug')->where('slug', 'like', $slug.'%')
        ->where('id', '<>', $id)
        ->get();
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


         $imageindex =  $_POST['imageid'];
         $currentservices =  DB::table('facility')
         ->where('id',  $imageindex)
         ->first();
            $values = array(
                'icons'=>""
            ); 
        
            DB::table('facility')
            ->where('id',$imageindex)
            ->update($values);
    }
    public function orderby()
    {
         $sectionid =  $_POST['sectionid'];
         $select    =  DB::table('faqcategories')->get();
        
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('faqcategories')
            ->where('id',$getkey)
            ->update($values);
         }
    }

}
