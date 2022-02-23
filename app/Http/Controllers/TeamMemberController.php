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
use App\TeamMember;
use Log;
use Illuminate\Support\Facades\DB;
use File;
use App\Location;
class TeamMemberController extends BaseController
{
	use RoutesAndGuards, ThemeAndViews, UserPages, UploadTrait;
    /**
     * Initialize public controller.
     *
     * @return null
     */
    public function __construct()
    {
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
    	$team = TeamMember::orderBy('order_by','asc')->get();
        
        $menus_title = DB::table('menus')->select('name')->where('slug','team-member')->first();
        $menus_title = $menus_title->name;
        
        
        
        return $this->response->setMetaTitle(__('app.dashboard'))
            ->view('team.index')
            ->data(compact('user', 'team','menus_title'))
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

        $team_member = TeamMember::find($id);
        return $this->response->setMetaTitle(trans('app.view') . ' ' . trans('user::team.name'))
            ->data(compact('team_member'))
            ->view('team.show')
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
        $menus_title = DB::table('menus')->select('name')->where('slug','team-member')->first();
        $menus_title = $menus_title->name;
        

        $locations   =  DB::table('locations')->orderBy('order_by','asc')->get();   
        
        return $this->response->setMetaTitle(trans('app.new') . ' ' . trans('user::team.name'))
            ->view('team.create', true)
            ->data(compact('menus_title','locations'))
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
            $team_member                       = TeamMember::create();
            $team_member->name                 = $request->name;
            $team_member->title                = $request->title;
            $team_member->email                = $request->email;
            $team_member->mobile               = $request->mobile;
            $team_member->address              = $request->address;
            $team_member->snippet              = $request->snippet;
            $team_member->bio                  = $request->bio;
            $team_member->qanda                = $request->qanda;
            $team_member->meta_title           = $request->meta_title;
            $team_member->meta_keyword         = $request->meta_keyword;
            $team_member->meta_description     = $request->meta_description;
            $slugs            = $request->slug;

                if($slugs)
                {
                   $team_member->slug  = $this->createSlug($request->slug);  
                }
                else
                {
                   $team_member->slug  = $this->createSlug($request->name);  
                }

            $team_member->status =  $request->status;
            $team_member->photo  = $request->profile_image;
            $team_member->locations = $request->locations;
//            if (!empty($image)) {
//
//                $name = config('app.name').'-'.str_slug($request->name).'-members';
//                $folder = '/uploads/';
//                $image = $request->file('profile_image');
//                $name  = rand(1000,9999).'.'.$image->getClientOriginalName();
//                $destinationPath = public_path('/storage/uploads/');
//                $filePath = $folder . $name;
//                $image->move($destinationPath, $name);
//                $team_member->photo = $filePath;
//            }
            $team_member->save();
            return $this->response->message(trans('messages.success.created', ['Module' => trans('user::team.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('team'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('team'))
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
        return TeamMember::select('slug')->where('slug', 'like', $slug.'%')
        ->where('id', '<>', $id)
        ->get();
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
        $team_member = TeamMember::find($id);
        $locations   =  DB::table('locations')->orderBy('order_by','asc')->get();
        
        return $this->response->setMetaTitle(trans('app.edit') . ' ' . trans('user::team.name'))
            ->view('team.edit')
            ->data(compact('team_member','locations'))
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
            $team_member = TeamMember::find($request->id);

            $team_member->name    = $request->name;
            $team_member->title   = $request->title;
            $team_member->email   = $request->email;
            $team_member->mobile  = $request->mobile;
            $team_member->snippet = $request->snippet;
            $team_member->bio     = $request->bio;
            $team_member->address = $request->address;
            $team_member->status  = $request->status;
            $team_member->photo   = $request->profile_image;
            $team_member->qanda                  = $request->qanda;
            $team_member->meta_title       = $request->meta_title;
            $team_member->meta_keyword     = $request->meta_keyword;
            $team_member->meta_description = $request->meta_description;
            $team_member->locations = $request->locations;
            
            
            
            $current_slug               = $request->slug;
            $existed_slug       = TeamMember::select('slug')->where('id',$request->id)->first()['slug'];
            if($existed_slug == $current_slug)
            {
                $team_member->slug = $current_slug;
            }
            else
            {
                $team_member->slug = $this->createSlug($current_slug);
            }
            
//            if (!empty($image)) {
//                $name = config('app.name').'-'.str_slug($request->name).'-members';
//                $folder = '/uploads/';
//                $image = $request->file('profile_image');
//                $name  = rand(1000,9999).'.'.$image->getClientOriginalName();
//                $destinationPath = public_path('/storage/uploads/');
//                $filePath = $folder . $name;
//                $image->move($destinationPath, $name);
//                $team_member->photo = $filePath;
//            }

            $team_member->save();


            return redirect()->back();


            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user::team.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('team'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('team/'))
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
            $delete = TeamMember::find($id)->delete();
        }  catch (Exception $e) {
           return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('team'))
                ->redirect();
        }  

    }



    public function create_slug($string){
       $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
       return strtolower($slug);
    }
    
    public function deleteiconsimage() 
    {
  
        $imageindex      =  $_POST['rowid'];
        $filepath       =  $_POST['imagepath'];
        $image_path = public_path("/storage".$filepath."");
        File::delete($image_path);
        $values = array(
            'photo'=>""
        ); 
        DB::table('members')
        ->where('id',$imageindex)
        ->update($values);
        
  
    }
    public function orderby()
    {
         $sectionid =  $_POST['sectionid'];
         $select    =  DB::table('members')->get();
        
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('members')
            ->where('id',$getkey)
            ->update($values);
         }
    }
    

}
