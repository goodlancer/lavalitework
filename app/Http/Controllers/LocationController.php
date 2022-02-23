<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Response\ResourceResponse;
use Litepie\Theme\ThemeAndViews;
use Litepie\User\Traits\RoutesAndGuards;
use Litepie\User\Traits\UserPages;
use App\Traits\UploadTrait;
use App\MySetting;
use App\Service;
use App\Facility;
use App\Building;
use App\Location;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use File;

class LocationController extends BaseController
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
        $data =  DB::table('locations')->orderBy('order_by','asc')->get();
        $menus_title = DB::table('menus')->select('name')->where('slug','locations-3')->first();
        $menus_title = $menus_title->name;
        
        return $this->response->setMetaTitle(__('app.service'))
            ->view('locations.index')
            ->data(compact('data','menus_title'))
            ->output();
    }

    public function create(Request $request)
    {
        $menus_title = DB::table('menus')->select('name')->where('slug','locations-3')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle(trans('app.new') . ' ' . trans('user::service.name'))
        ->view('locations.create', true)
        ->data(compact('menus_title'))
        ->output();
    }

    public function store(Request $request)
    {
        try {
            
            $location         = Location::create();
            $location->title  = $request->title;
  
            $location->status = $request->status;
            $image            = $request->store_img;
            $slugs            = $request->slug;

            if($slugs)
            {
               $location->slug  = $this->createSlug($request->slug);  
            }
            else
            {
               $location->slug  = $this->createSlug($request->title);  
            }

            $location->mobile = $request->mobile;
            $location->address= $request->address;
            $location->timing = $request->timing;
            $location->star   = $request->star;
            $location->rating = $request->rating;
            
            
            
            $location->meta_title       =$request->meta_title;
            $location->meta_keyword     =$request->meta_keyword;
            $location->meta_description =$request->meta_description; 
            $filePath = '';

             $location->image  = $request->store_img;
             $location->save();
            
             return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
             ->code(204)
            ->status('success')
            ->url(guard_url('locations'))
            ->redirect();
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('locations'))
                ->redirect();
        }

    }
    public function createSlug($title, $id = 0)
    {
        $slug     = str_slug($title);
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
        return Location::select('slug')->where('slug', 'like', $slug.'%')
        ->where('id', '<>', $id)
        ->get();
    }
    
    public function edit($id)
    {
            $data = DB::table('locations')
               ->where('id',$id)->first();
            return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::makediffent.title'))
            ->view('locations.edit')
            ->data(compact('data'))
            ->output();

    }
    
    public function update(Request $request)
    {

        try {                           
               
            $location = Location::find($request->id);
            
            $location->title  = $request->title;
            

            $location->status = $request->status;
            
          
            $location->mobile  = $request->mobile;
            $location->address = $request->address;
            $location->timing  = $request->timing;
            $location->star    = $request->star;
            $location->rating  = $request->rating;
            $image             = $request->store_img;
            $filePath = '';
            
            
            $current_slug               = $request->slug;
            $existed_slug       = Location::select('slug')->where('id',$request->id)->first()['slug'];
            if($existed_slug == $current_slug)
            {
                $slug = $current_slug;
            }
            else
            {
                $slug = $this->createSlug($current_slug);
            }
            
            $location->meta_title       =$request->meta_title;
            $location->meta_keyword     =$request->meta_keyword;
            $location->meta_description =$request->meta_description; 
            
            
            
             $location->slug = $slug;
            
             $location->image  = $request->store_img;
            
             $location->save();
             return redirect()->back(); 
            
            
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('locations/'))
                ->redirect();
        }

    }
    public function destroy()
    {    
        try {
            $id = $_POST['rowid'];
            $delete = Location::find($id)->delete();
        }  catch (Exception $e) {
           return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('locations'))
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


    public function deletelocation()
    {
        $filepath   = $_POST['filepath'];
        
   
        $image_path = public_path("/storage".$filepath."");
        File::delete($image_path);
        $row_id =  $_POST['imageid'];
        $values = array(
            'image'=>""
        ); 
        DB::table('locations')
        ->where('id',$row_id)
        ->update($values);   
    }

    public function orderby()
    {
         $sectionid =  $_POST['sectionid'];
         $select    =  DB::table('locations')->get();
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('locations')
            ->where('id',$getkey)
            ->update($values);
         }
    }

    

    
    
    
    
    

}
