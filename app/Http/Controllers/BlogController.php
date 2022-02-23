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
use App\Blog;
use App\Blogcategories;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use File;
class BlogController extends BaseController
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
    	$user     = $request->user()->toArray();
    	$services = Blog::orderBy('order_by','asc')->get();
    	
        $menus_title       = DB::table('menus')->select('name')->where('slug','blogs')->first();
        $menus_title       = $menus_title->name;

        return $this->response->setMetaTitle($menus_title)
            ->view('blog.index')
            ->data(compact('user', 'services','menus_title'))
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
    public function show($id)
    {
        $service = Service::find($id);
        
        return $this->response->setMetaTitle(trans('app.view') . ' ' . trans('user::service.name'))
            ->data(compact('service'))
            ->view('service.show')
            ->output();
    }

    /**
     * Show the form for creating a new team.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request)
    {
        

        
        $menus    = DB::table('menus')->where('slug', 'blog-categories')->first();
        
        
        $category = DB::table('menus')->where('parent_id',$menus->id)->get();
        
        $allcategories     = Blogcategories::where('status','published')->get();
        
        $menus_title       = DB::table('menus')->select('name')->where('slug','blogs')->first();
        
        
    
        
        $menus_title       = "New ".$menus_title->name;
        
        
        return $this->response->setMetaTitle($menus_title)
        ->view('blog.create', true)
        ->data(compact('category','menus_title','allcategories'))
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
            $title                        = $request->name;
            $image                        = $request->featured_img;
            $author                       = $request->author;
            $info                         = $request->info;
            $filePath                     = '';
            $filePath1                    = '';
            $publish_date                 = $request->publish_date;
            $category_id                  = $request->category;
            $status                       = $request->status;
            $short_descriptions           = $request->short_descriptions;
            $meta_title                   = $request->meta_title;
            $meta_description             = $request->meta_description;
            $meta_keyword                 = $request->meta_keyword;
            $category                     = $request->category;
            $heading                      = $request->heading;

            $slugs                        = $request->slug;
            $filePath                     = $request->image;
            
            
            if($slugs)
            {
                   $slug  = $this->createSlug($request->slug);  
            }
            else
            {
                   $slug  = $this->createSlug($request->name);  
            }
            

            
            
            
            $values = array(
                'name' =>$title,
                'icon'=>$filePath,
                'status'=>$status,
                'info'=>$info,
                'slug'=>$slug,
                'publish_date'=>$publish_date,
                'category_id'=>$category_id,
                'short_descriptions'=>$short_descriptions,
                'meta_title'=>$meta_title,
                'meta_description'=>$meta_description,
                'meta_keyword'=>$meta_keyword,
                'heading'=>$heading,
                'category'=>$category,
                'author'=>$author
            );
            
            DB::table('blogs')->insert($values);
            
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
            ->code(204)
            ->status('success')
            ->url(guard_url('blogs'))
            ->redirect();
            
            
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('blogs'))
                ->redirect();
        }

    } 
    public function edit($id)
    {
                $data    = Blog::find($id);
                $menus    = DB::table('menus')->where('slug', 'blog-categories')->first();
        
                $category = DB::table('menus')->where('parent_id',$menus->id)->get();
                $allcategories     = Blogcategories::where('status','published')->get();
                return $this->response->setMetaTitle('Blogs-Update')
                ->view('blog.edit')
                ->data(compact('data','category','allcategories'))
                ->output();

    }
    public function update(Request $request)
    {

        try {                           
            $title        = $request->name;
            $image        = $request->featured_img;
            $info         = $request->info;
            $filePath     = '';
            $publish_date     = $request->publish_date;
            $category_id      = $request->category;
            $status           = $request->status;
            $short_descriptions           = $request->short_descriptions;
            $meta_title                 = $request->meta_title;
            $meta_description           = $request->meta_description;
            $meta_keyword               = $request->meta_keyword;
            $heading                      = $request->heading;
            $plans_image                  = $request->plans_image;
            $category                      = $request->category;
            $author                      = $request->author;
            
             $existed_slug               = Blog::select('slug')->where('id',$request->id)->first()['slug'];
             $current_slug               = $request->slug;
             if($existed_slug == $current_slug)
             {
                $slug = $current_slug;
             }
             else
             {
                 $slug = $this->createSlug($current_slug);
             }
            
             $filePath = $request->image;
            
       
            
            $values = array(
                
                'name' =>$title,
                'icon'=>$filePath,
                'status'=>$status,
                'info'=>$info,
                'publish_date'=>$publish_date,
                'category_id'=>$category_id,
                'short_descriptions'=>$short_descriptions,
                'meta_title'=>$meta_title,
                'meta_description'=>$meta_description,
                'meta_keyword'=>$meta_keyword,
                'heading'=>$heading,
                'category'=>$category,
                'slug'=>$slug,
                'author'=>$author,
            ); 
            DB::table('blogs')
            ->where('id',  $request->id)
            ->update($values);
            return redirect()->back();
         
            } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('blogs/'))
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
        return Blog::select('slug')->where('slug', 'like', $slug.'%')
        ->where('id', '<>', $id)
        ->get();
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
            $service = Blog::find($id);
            $service->delete();
            
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::service.name')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('blogs/'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('service'))
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


    public function uploadprofile(Request $request)
    {
            $username   =  user()->name;
            $users      =   DB::table('users')->where('name',$username)->get();
            $user_id = $users[0]->id;
      
            $image = $request->image;
            if (!empty($image)) {
                $folder = '/uploads/images/services/';
                $image = $request->file('image');
                $name  = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/storage/uploads/images/services/');
                $filePath = $folder . $name;
                $image->move($destinationPath, $name);
                $url = url('admin/profile');
                $values = array(
                    'image'=>$name
                 ); 
                DB::table('users')
                ->where('id',$user_id)
                ->update($values);
                return redirect()->away($url);
                
            }

    }
    public function uploaduserprofile(Request $request)
    {

             $username   =  $request->username;
             $users      =  DB::table('users')->where('name',$username)->get();
             $user_id    =  $users[0]->id;
             $image      =  $request->image;

                if (!empty($image)) {
                $folder  = '/uploads/images/services/';
                $image   = $request->file('image');
                $name    = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/storage/uploads/images/services/');
                $filePath = $folder . $name;
                $image->move($destinationPath, $name);
                $url    = url('admin/user/user');
                $values = array(
                'image'=>$name
                ); 

           
                DB::table('users')
                ->where('id',$user_id)
                ->update($values);

                return redirect()->away($url);
                
            }

    }
    
    public function deletefeaturedimage()
    {
        
         $rowid           =  $_POST['rowid'];
        
     
         $values = array(
            'icon'=>""
         ); 
         DB::table('blogs')
         ->where('id',$rowid)
         ->update($values);   
    }
    public function deleteplansimage()
    {

         $filepath        =  $_POST['imagepath'];
         $rowid           =  $_POST['rowid'];
         $image_path = public_path("/storage".$filepath."");
         File::delete($image_path);
         $values = array(
            'plans_image'=>""
         ); 
         DB::table('blogs')
         ->where('id',$rowid)
         ->update($values); 
    }
    public function orderby()
    {
          $sectionid =  $_POST['sectionid'];
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('blogs')
            ->where('id',$getkey)
            ->update($values);
         }
    }
}