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
use App\Library;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use Session;
class ImageCropperController extends Controller
{

    public function index()
    {
        return view('cropper');
    }

    public function upload(Request $request)
    {

        $userid         =  $request->userid;
        $filename       =  $request->filename;
        $folderPath     =  public_path('storage/uploads/');
        $folder         =  'storage/uploads/';
        $image_parts    =  explode(";base64,", $request->image);
        $image_type_aux =  explode("image/", $image_parts[0]);
        $image_type     =  $image_type_aux[1];
        $image_base64   =  base64_decode($image_parts[1]);
        //echo '<pre>',var_dump( $image_type ); echo '</pre>';
        // die();
        $filename       = substr($filename,0,3).''.uniqid().'.'.$image_type;
        $file           = $folderPath .$filename;
        $created_at     = date('Y-m-d H:i:s');
        $ourgallerypath = $folderPath.$filename;
       
        
        
        $values = array('profile' =>$filename,'updated_at' => $created_at,'created_at'=>$created_at);
        DB::table('users')->where('id',$userid)->update($values);
        file_put_contents($file, $image_base64);
        return response()->json(['success'=>'success']);
    }
}