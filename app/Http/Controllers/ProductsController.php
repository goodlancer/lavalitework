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
use App\Products;
use App\Productcategories;
use App\Auction;
use App\Library;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;


class ProductsController extends BaseController
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
        $user          = $request->user()->toArray();
        $menus_title   = DB::table('menus')->select('name')->where('slug','products')->first();
        $products      = Products::orderBy('order_by','asc')->get();
      
//        foreach($products as $keys=>$vals){
//              echo '<pre>',var_dump($vals->order_by); echo '</pre>';
//        }
      
        $allcategories = Productcategories::where('status','published')->get();
        
        $menus_title   = $menus_title->name;
        return $this->response->setMetaTitle($menus_title)
        ->view('products.index')
        ->data(compact('user', 'products','menus_title','allcategories'))
        ->output();
        
        
        
        
        
    }

    public function create(Request $request)
    {
        $menus_title = DB::table('menus')->select('name')->where('slug','products')->first();
        $menus_title = $menus_title->name;
        $allcategories     = Productcategories::where('status','published')->get();
        return $this->response->setMetaTitle($menus_title)
        ->view('products.create', true)
        ->data(compact('menus_title','allcategories'))
        ->output(); 
        
    }
    public function store(Request $request)
    {
      try {
          
            $products         = Products::create();
          
            $product_counts   = Products::all()->count();
          
          
          
          
        
            $products->title  = $request->title;
            $products->status = $request->status;
          
            $products->image                       = $request->image;
            $products->price                       = $request->price;
            $products->saleprice                   = $request->saleprice;
            $products->home_hotitem                = $request->home_hotitem;
          
            $products->specification              = $request->specification;
            $products->descriptions               = $request->descriptions;
            $slug                                 = $this->createSlug($request->title);  
            $products->slug                       = $slug;
          
            $products->shortdescriptions               = $request->shortdescriptions;
          
          
            $products->order_by                        = $product_counts;
          
            //$portfolios->category    = $request->category;
            $arraytype2 = array();
            $category_h = $request->category;

            
            if($category_h)
            {
               $category = serialize($category_h);
               $products->category = $category; 
            }
            else
            {
                $products->category = serialize($arraytype2); 
            }    

            $portfolios_gallery      = $request->gallery;
            $gallerydata = [];
            if($portfolios_gallery){
                $gallerydata = explode(",",$portfolios_gallery);  
            }
            if(count($gallerydata) > 0 ){
                foreach($gallerydata as $gkey=>$gval){
                    if($gval){
                        $getgallery             = Library::find($gval);
                        $getgallerypath[$gval]  = "public/".$getgallery['filename'];
                    }
                } 
            }
            else{
                $getgallerypath  = [];
            }
        
            $gallery                 = serialize($getgallerypath);
            $products->gallery       = $gallery;
            $products->save();
          
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
                   ->code(204)
                   ->status('success')
                   ->url(guard_url('products'))
                   ->redirect();
            
           } 
           catch (Exception $e) {
                return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('products'))
                ->redirect();
           }
        

    }
    public function edit($id)
    {
          $data = Products::find($id);
           $allcategories     = Productcategories::where('status','published')->get();
          return $this->response->setMetaTitle('products')
          ->view('products.edit')
          ->data(compact('data','allcategories'))
          ->output();
        
    }
    public function update(Request $request)
    {

        try {    

            $products                     = Products::find($request->id);
            $products->title              = $request->title;
            $products->status             = $request->status;
            $products->image              = $request->image;
            $products->price              = $request->price;
            $products->home_hotitem               = $request->home_hotitem;
            $products->saleprice                  = $request->saleprice;
            $products->specification              = $request->specification;
            $products->descriptions               = $request->descriptions;
            $products->shortdescriptions               = $request->shortdescriptions;

            $products->meta_title         = $request->meta_title;
            $products->meta_keyword       = $request->meta_keyword;
            $products->meta_description   = $request->meta_description; 
            
             $existed_slug               = Products::select('slug')->where('id',$request->id)->first()['slug'];
             $current_slug               = $request->slug;
             if($existed_slug == $current_slug)
             {
                $slug = $current_slug;
             }
             else
             {
                 $slug = $this->createSlug($current_slug);
             }
            

            $arraytype2 = array();
            $category_h = $request->category;

            $products->slug = $slug;
            
            
            if($category_h)
            {
               $category = serialize($category_h);
               $products->category = $category; 
            }
            else
            {
                $products->category = serialize($arraytype2); 
            }    

            $portfolios_gallery = $request->gallery;

            $gallerydata = [];
            if($portfolios_gallery){
                $gallerydata = explode(",",$portfolios_gallery);  
            }
            
            
      
            $oldfile = array();
            if($products->gallery)
            {
                foreach(unserialize($products->gallery) as $keyc=>$pathval) 
                {
                    $oldfile[] = $pathval;
                }
            }

   
            
  
            if(count($gallerydata) > 0 ){
                
    
                
                foreach($gallerydata as $gkey=>$gval){
                    if($gval){
                        $getgallery             = Library::find($gval);
                        $getgallerypath[]  = "public/".$getgallery['filename'];  
                    }  
                } 
  
                
                        if(count($oldfile) > 0 )
                        {
                            $ourgallerypathjoin     =  array_merge($getgallerypath,$oldfile);
                            $products->gallery      =  serialize($ourgallerypathjoin);

                        }
                        else
                        {
                            $products->gallery    = serialize($getgallerypath);
                        }  
                
                
            }
            else{
                $getgallerypath          = serialize($oldfile);
                $gallery                 = $getgallerypath;
                $products->gallery       = $gallery;
            }
            
            $products->save();
            
            return redirect()->back();
            
            } 
            catch (Exception $e) {
                
                return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('categories/'))
                ->redirect();
                
            }
    }
    
    
    public function importcsv(Request $request){
        
        
     $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    // Allowed mime types
        // Validate whether selected file is a CSV file
        if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)){
            // If the file is uploaded
            if(is_uploaded_file($_FILES['file']['tmp_name'])){
                // Open uploaded CSV file with read-only mode
                $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
                // Skip the first line
                fgetcsv($csvFile);
                // Parse data from CSV file line by line
                while(($line = fgetcsv($csvFile)) !== FALSE){
                    // Get row data
                  //  $id               = $line[0];
                    $product_title      = $line[0];
                    $sku                = $line[1];
                    $categories         = $line[2];
                    
                    
                    
                    echo $categories;
                    die();
 
                 //   $prevQuery = DB::table('products')->where('id',$line[0])->get()[0]->id;
                    
//                    if($prevQuery){
//                        
//                        $values = array(
//                           'title'  => $product_title,
//                           'price'  => $regularprice,
//                           'status' => $status,
//                        );
//                        DB::table('products')
//                        ->where('id',$id)
//                        ->update($values); 
//                        
//                     }
//                     else{
                            $products         = Products::create();
                            $products->title  = $product_title;
                            $products->price  = $regularprice;
                            $products->status = $status;
                            $slug             = $this->createSlug($name);  
                            $products->slug   = $slug;
                            $products->save();
                    //  }
                    
                }
                // Close opened CSV file
                fclose($csvFile);

                $qstring = '?status=succ';
                return redirect()->back();     
            }
            else{
                $qstring = '?status=err';
            }
        }else{
            $qstring = '?status=invalid_file';
              return redirect()->back();     
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
        return Products::select('slug')->where('slug', 'like', $slug.'%')
        ->where('id', '<>', $id)
        ->get();
    }
    
    
    
    
    public function destroy()
    {
        
        try { 
            $post_id = $_POST['rowid'];
            $facility = Products::find($post_id);
            $facility->delete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::facility.title')]))
                   ->code(202)
                   ->status('success')
                   ->url(guard_url('products/'))
                   ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('products'))
                ->redirect();
        }

    }
    public function create_slug($string){
       $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
       return strtolower($slug);
    }


    
    public function orderby()
    {
        
          $sectionid =  $_POST['sectionid'];
        
        // echo '<pre>',var_dump($sectionid); echo '</pre>';
        
          //die();
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('products')
            ->where('id',$getkey)
            ->update($values);
         }
        
    } 
    
        
    public function deleteimage() 
    {
        $imageindex =  $_POST['imageid'];
        $id         =  $_POST['locationid'];

        $currentlocation =  DB::table('products')
        ->where('id',  $id)
        ->first();
        $ourgallerypathnew = array();
        $ourgallerypath = array();
        if($currentlocation->gallery)
        {
            foreach(unserialize($currentlocation->gallery) as $keyc=>$pathval) 
            {
                if($keyc != $imageindex)
                {
                   $ourgallerypath[] = $pathval;
                }

            }
        }
        $values = array(
        'gallery'=>serialize($ourgallerypath)
        ); 
        DB::table('products')
        ->where('id',$id)
        ->update($values);
    }
     public function removefeatured_image() 
    {
    
        $imageindex =  $_POST['imageid'];
        $currentservices =  DB::table('products')
        ->where('id',  $imageindex)
        ->first();
        $values = array(
            'image'=>""
        ); 

        DB::table('products')
        ->where('id',$imageindex)
        ->update($values);
            
    }
    public function products_galleryorderby()
    {
        $id       = $_POST['post_id'];
        $all_path = $_POST['allpath'];
        $gallery       =  serialize($all_path);
        $bvalues = array(
            'gallery'=>$gallery
        );
        $data = DB::table('products')->where('id',$id)->update($bvalues);   
    }

}
