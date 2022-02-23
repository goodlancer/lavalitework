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
use App\Review;

use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use File;

class ReviewController extends BaseController
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
        $data =  DB::table('reviews')->orderBy('id', 'DESC')->get();
        
        $menus_title       = DB::table('menus')->select('name')->where('slug','client-reviews')->first();
        $menus_title       = $menus_title->name;
        
        
        return $this->response->setMetaTitle($menus_title)
            ->view('reviews.index')
            ->data(compact('data','menus_title'))
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
        $service = Buildings::find($id);
        
        return $this->response->setMetaTitle(trans('app.view') . ' ' . trans('user::service.name'))
            ->data(compact('service'))
            ->view('reviews.show')
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
        
        $menus_title       = DB::table('menus')->select('name')->where('slug','client-reviews')->first();
        $menus_title       = "New ".$menus_title->name;
        
              return $this->response->setMetaTitle($menus_title)
              ->view('reviews.create', true)
              ->data(compact('menus_title'))
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
            
            $rating  = $request->rating;
            $status  = $request->status;
            $image   = $request->image;
            $url   = $request->url;
            $title   = $request->title;
             $comments   = $request->comments;
             $ratingnumber   = $request->ratingnumber;
            $filePath = '';

            
            if (!empty($image)) {
               
                $folder                     = '/uploads/images/';
                $image                      = $request->file('image');
                $file_slug_name             = $image->getClientOriginalName();
                $file_slug_name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_slug_name);
                $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_slug_name_without_ext)));
                $name                       = $file_slug_name_without_ext.''.time().'.'.$image->getClientOriginalExtension();
                $destinationPath            = public_path('/storage/uploads/images/');
                $filePath                   = $folder . $name;
                $image->move($destinationPath, $name);
            }
            
            
            $values = array(
                            'status'=>$status,
                            'rating'=>$rating,
                            'icons'=>$filePath,
                            'comments'=>$comments,
                            'ratingnumber'=>$ratingnumber,
                            'url'=>$url,
                            'title'=>$title
                           );
            
            
            DB::table('reviews')->insert($values);
            
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
            ->code(204)
            ->status('success')
            ->url(guard_url('reviews'))
            ->redirect();
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('reviews'))
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
                 
            $data = DB::table('reviews')
               ->where('id',$id)->first();
        
       //echo '<pre>',var_dump($data); echo '</pre>';
        
       
            return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::makediffent.title'))
            ->view('reviews.edit')
            ->data(compact('data'))
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

        try {                           
               
            $rating  = $request->rating;
            $status  = $request->status;
            $image   = $request->image;
            $title   = $request->title;
            $comments   = $request->comments;
            $ratingnumber   = $request->ratingnumber;
            $url   = $request->url;
            $filePath = '';


            if (!empty($image)) {
               
                $folder                     = '/uploads/images/';
                $image                      = $request->file('image');
                $file_slug_name             = $image->getClientOriginalName();
                $file_slug_name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_slug_name);
                $file_slug_name_without_ext = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $file_slug_name_without_ext)));
                $name                       = $file_slug_name_without_ext.''.time().'.'.$image->getClientOriginalExtension();
                $destinationPath            = public_path('/storage/uploads/images/');
                $filePath                   = $folder . $name;
                $image->move($destinationPath, $name);
            }
            else
            {
                $filePath = $request->isicons;
            }
            
            
            $values = array(
                            'status'=>$status,
                            'rating'=>$rating,
                            'icons'=>$filePath,
                            'url'=>$url,
                            'comments'=>$comments,
                            'ratingnumber'=>$ratingnumber,
                            'title'=>$title
                           );
            
            DB::table('reviews')
            ->where('id',  $request->id)
            ->update($values);
                
            return redirect()->back();
            
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('reviews/'))
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
            DB::delete('delete from reviews where id = ?',[$id]);
            
           
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::success')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('reviews/'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('reviews'))
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


    public function deletebrands()
    {
        $filepath   = $_POST['filepath'];
        $image_path = public_path("/storage".$filepath."");
        File::delete($image_path);
        $row_id =  $_POST['imageid'];
        $values = array(
            'icons'=>""
        ); 
        DB::table('reviews')
        ->where('id',$row_id)
        ->update($values);   
    }
    
    
    
    
    

    
    
    
    
    

}
