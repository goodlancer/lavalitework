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
use App\Blogcategories;
use App\Category;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;

class BlogcategoriesController extends BaseController
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
    	$allcategories     = Blogcategories::orderBy('order_by','asc')->get();
        $menus_title       = DB::table('menus')->select('name')->where('slug','blog-categories')->first();
        $menus_title       = $menus_title->name;
        
        return $this->response->setMetaTitle($menus_title)
            ->view('blogcategories.index')
            ->data(compact('menus_title','allcategories'))
            ->output();
    }

    public function create(Request $request)
    {
        

              $menus_title = DB::table('menus')->select('name')->where('slug','faqs-categories')->first();
              $menus_title = $menus_title->name;
        
              $category = Blogcategories::all();
              return $this->response->setMetaTitle($menus_title)
              ->view('blogcategories.create', true)
              ->data(compact('category','menus_title'))
              ->output();
    }

    public function store(Request $request)
    {
      try {
              $blogcategories                = Blogcategories::create();
              $blogcategories->name          = $request->name;
              $blogcategories->created_at    = $request->created_at;
              $blogcategories->slug          = $this->createSlug($request->name);
              $blogcategories->status        = $request->status;
              $blogcategories->save();

              return $this->response->message('success')
                   ->code(204)
                   ->status('success')
                   ->url(guard_url('blogcategories'))
                   ->redirect();
            
           } 
           catch (Exception $e) {
                return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('blogcategories'))
                ->redirect();
           }

    }

    public function edit($id)
    {
          $data = Blogcategories::find($id);
          return $this->response->setMetaTitle('Categories')
          ->view('blogcategories.edit')
          ->data(compact('data'))
          ->output();
    }

    public function update(Request $request)
    {

        try {    
                $blogcategories                 = Blogcategories::find($request->id);
                $blogcategories->name           = $request->name;
                $blogcategories->created_at     = $request->created_at;
                $blogcategories->status         = $request->status;
                $blogcategories->save();  
                return redirect()->back();
            } 
            catch (Exception $e) {
                return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('blogcategories/'))
                ->redirect();
            }
    }

    public function destroy()
    {
       try {
            $id = $_POST['rowid'];
            $delete = Blogcategories::find($id)->delete();
        }  catch (Exception $e) {
           return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('blogcategories'))
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
        return Blogcategories::select('slug')->where('slug', 'like', $slug.'%')
        ->where('id', '<>', $id)
        ->get();
    }

    public function orderby()
    {
          $sectionid =  $_POST['sectionid'];
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('blogcategories')
            ->where('id',$getkey)
            ->update($values);
         }
    }

}
