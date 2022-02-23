<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Response\ResourceResponse;;
use Litepie\Theme\ThemeAndViews;
use Litepie\User\Traits\RoutesAndGuards;;
use Litepie\User\Traits\UserPages;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File; 
use App\Traits\UploadTrait;
use App\MySetting;
use App\Service;
use App\Facility;
use App\Location;
use App\Library;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use Session;
use Validator;
class LibraryController extends BaseController
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
    	$user    = $request->user()->toArray();
    	$library = Library::orderBy('id', 'desc')->get();
        $menus_title ="";
        return $this->response->setMetaTitle($menus_title)
            ->view('library.index')
            ->data(compact('user', 'library','menus_title'))
            ->output();
    }

    public function create(Request $request)
    {
        $menus_title ="";
              $user = $request->user()->toArray();
    	       $library = Library::orderBy('id', 'desc')->get();
              return $this->response->setMetaTitle($menus_title)
              ->view('location.create', true)
              ->data(compact('user','page_setting','menus_title'))
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

            $ourgallerypath=array();
            if($files=$request->file('ourlibrary')){
                
                   foreach($files as $key=>$file){
                       
                        $folder   = 'storage/uploads/';
                        $name     = uniqid().rand(0,1000).str_replace(' ', '', $file->getClientOriginalName());
                        $fileSize = $file->getClientSize();
                        $ext = $file->getClientOriginalExtension();
                        $original_name = str_replace(' ', '', $file->getClientOriginalName());   
                        $destinationPathGallery = public_path('storage/uploads/');
                        $ourgallerypath = $folder . $name;
                        $file->move($destinationPathGallery,$name);
                        $created_at = date('Y-m-d H:i:s');
                        $values = array('filename'=>$ourgallerypath,'updated_date' => $created_at,'created_date'=>$created_at,'filesize'=>$fileSize,'extension'=>$ext,'originalname'=>$original_name);
                        DB::table('library')->insert($values);
                        
                        
                   }
                     
            }
    
            return redirect()->back();        
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('media'))
                ->redirect();
        }

    }
    
   public function getselectimagedetails()
   {
       $row_id =  $_POST['alldata']['rowid'];
       $library = Library::where('id',$row_id)->get();


       $data = '<ul tabindex="-1" class="attachments ui-sortable ui-sortable-disabled" id="__attachments-view-47">';
       foreach ($library as $key => $value) {
        if($value->extension == "pdf"){
            $img = '<img src="'.url('public/themes/public/assets/images/pdf.png').'" data-src="'.$value->filename.'" >';
        }
        else if($value->extension == "docx" || $value->extension == "doc"){
            $img = '<img src="'.url('public/themes/public/assets/images/google-docs.png').'" data-src="'.$value->filename.'" >';
        }
        else{
            $img = '<img src="'.url('public').'/'.$value->filename.'">';
        }

        $data .='<li tabindex="0" role="checkbox"  class="attachment save-ready" data-id="'.$value->id.'">
                    <h4>Attachment Details</h4>
                    <div class="files-details">

                       <p>'.$value->originalname.'</p>

                    </div>
                    <div class="attachment-preview js--select-attachment type-image subtype-svg+xml landscape">
                        <div class="thumbnail">
                            <div class="centered">
                              '.$img.'
                            </div>
                            <input type="hidden" name="dbimagepath" value="'.$value->filename.'" class="selectfeatured_image">
                            
                        </div>
                    </div>
             </li>';
       }

       $data .='</ul>';
   
       return $data;
   }
    
    
    
    public function getmedialibrary(){

        
       $page_id =  $_POST['alldata']['page_id'];
        
        
//       echo $page_id;
//        
//       die();
        
        
        
       $library = Library::orderBy('id','desc')->paginate($page_id);
        
        
       $dbrows = Library::count();
        
        
        
//        echo '<pre>',var_dump($dbrows); echo '</pre>';
//        die();
//        
        
        
        
       $data = '<ul tabindex="-1" class="attachments ui-sortable ui-sortable-disabled" id="__attachments-view-47">';
       foreach ($library as $key => $value) {
        if($value->extension == "pdf"){
            $img = '<img src="'.url('public/themes/public/assets/images/pdf.png').'" data-src="'.$value->filename.'" >';
        }
        else if($value->extension == "docx" || $value->extension == "doc"){
            $img = '<img src="'.url('public/themes/public/assets/images/google-docs.png').'" data-src="'.$value->filename.'" >';
        }
        else{
            $img = '<img src="'.url('public').'/'.$value->filename.'">';
        }
        $data .='<li tabindex="0" role="checkbox"  class="attachment save-ready" data-id="'.$value->id.'">
                    <div class="attachment-preview js--select-attachment type-image subtype-svg+xml landscape">
                        <div class="thumbnail">
                            <div class="centered">
                              '.$img.'
                            </div>
                        </div>
                    </div>
                </li>';
       }

       $data .='</ul>';
        
        if($page_id < $dbrows ):
           $data .='<p class="loadmore">Load More</p>';
        endif;
        
        
       return $data;
   }
    
    
    public function getlibrary()
    {
      $library = Library::orderBy('id', 'desc')->get();
      $data = '';
       foreach ($library as $key => $value) {
         $data .='<div class="col-xl-4 col-lg-4 col-12">
                 <div class="wrap-image">
                        <img src="'.url('public').'/'.$value->filename.'">
                  </div>
             </div>';
       }
       return $data;
    }
    public function deleteimage() 
    {
        $image_id  = $_POST['alldata']['imageid'];
        $imagepath = $_POST['alldata']['imagepath'];
        
        $image_path_d = public_path($imagepath);
        File::delete($image_path_d); 
        DB::table('library')
        ->where('id',$image_id)
        ->delete();
    }
    public function storeMultiFile(Request $request)
    {
         
       $validatedData = $request->validate([
          'files' => 'required',
           'files.*' => ''
        ]);
 

        if($request->TotalFiles > 0)
        {
                
           for ($x = 0; $x < $request->TotalFiles; $x++) 
           {
 
               if ($request->hasFile('files'.$x)) 
               {

                        $file     = $request->file('files'.$x);
                        $folder   = 'storage/uploads/';
                        $name     = uniqid().rand(0,1000).str_replace(' ', '', $file->getClientOriginalName());
                        $fileSize = $file->getClientSize();
                        $ext = $file->getClientOriginalExtension();
                        $original_name = str_replace(' ', '', $file->getClientOriginalName());
                        $destinationPathGallery = public_path('storage/uploads');
                        $ourgallerypath = $folder . $name;
                        $file->move($destinationPathGallery,$name);

                        $created_at = date('Y-m-d H:i:s');
                        $values = array('filename'=>$ourgallerypath,'updated_date' => $created_at,'created_date'=>$created_at,'filesize'=>$fileSize,'extension'=>$ext,'originalname'=>$original_name);
                        DB::table('library')->insert($values);

  
                }
           }
           return response()->json(['success'=>'Ajax Multiple fIle has been uploaded']);
 
          
        }
        else
        {
           return response()->json(["message" => "Please try again."]);
        }
 
    }


}
