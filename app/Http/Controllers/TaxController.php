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
use App\Tax;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use File;

class TaxController extends BaseController
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
    	$user        =  $request->user()->toArray();
        $data        =  DB::table('taxes')->orderBy('id', 'asc')->get();
        $menus_title = DB::table('menus')->select('name')->where('slug','taxes')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle($menus_title)
            ->view('tax.index')
            ->data(compact('data','menus_title'))
            ->output();
    }
    public function create(Request $request)
    {
          $menus_title = DB::table('menus')->select('name')->where('slug','taxes')->first();
          $menus_title = $menus_title->name;
          return $this->response->setMetaTitle($menus_title)
          ->view('tax.create', true)
          ->data(compact('data','menus_title'))
          ->output();
    }

    public function store(Request $request)
    {
       
        try {
            
             $tax                 = Tax::create();
             $tax->rate           = $request->rate;
             $tax->country        = $request->country;
             $tax->state          = $request->state;


             $zipcode         = $request->zipcode;
             $implode_zipcode = explode(",",$zipcode);
             $tax->zipcode    = serialize($implode_zipcode);

             $tax->save();
            
            
            
             return $this->response->message(trans('messages.success.created', ['Module' => trans('user::facility.name')]))
             ->code(204)
             ->status('success')
             ->url(guard_url('taxes'))
             ->redirect();
                
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('taxes'))
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
            
        
            $data = DB::table('taxes')
               ->where('id',$id)->first();
            $title = $data->state;
       
            return $this->response->setMetaTitle($title)
            ->view('tax.edit')
            ->data(compact('data','title'))
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
        try{  
            
            
             $tax                  = Tax::find($request->id);
             $tax->rate            = $request->rate;
             $tax->country         = $request->country;
             $tax->state           = $request->state;
             $zipcode         = $request->zipcode;
             $implode_zipcode = explode(",",$zipcode);
             $tax->zipcode    = serialize($implode_zipcode);
             $tax->save();
             return redirect()->back(); 
            
            } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('taxes/'))
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
            $delete = Tax::find($id)->delete();
        }  catch (Exception $e) {
        return $this->response->message($e->getMessage())
            ->code(400)
            ->status('error')
            ->url(guard_url('taxes'))
            ->redirect();
        }  
    }


    public function process_request(){
        
        
            $country = $_POST["country"];
        
        

$row_id  = $_POST["rowid"];
$selected_state ="";
if($row_id){
    $data = Tax::find($row_id)->first();    
    $selected_state = $data->state;    
}        

        
$countryArr = array(
"US" => array('AL'=>"Alabama",  
'AK'=>"Alaska",  
'AZ'=>"Arizona",  
'AR'=>"Arkansas",  
'CA'=>"California",  
'CO'=>"Colorado",  
'CT'=>"Connecticut",  
'DE'=>"Delaware",  
'DC'=>"District Of Columbia",  
'FL'=>"Florida",  
'GA'=>"Georgia",  
'HI'=>"Hawaii",  
'ID'=>"Idaho",  
'IL'=>"Illinois",  
'IN'=>"Indiana",  
'IA'=>"Iowa",  
'KS'=>"Kansas",  
'KY'=>"Kentucky",  
'LA'=>"Louisiana",  
'ME'=>"Maine",  
'MD'=>"Maryland",  
'MA'=>"Massachusetts",  
'MI'=>"Michigan",  
'MN'=>"Minnesota",  
'MS'=>"Mississippi",  
'MO'=>"Missouri",  
'MT'=>"Montana",
'NE'=>"Nebraska",
'NV'=>"Nevada",
'NH'=>"New Hampshire",
'NJ'=>"New Jersey",
'NM'=>"New Mexico",
'NY'=>"New York",
'NC'=>"North Carolina",
'ND'=>"North Dakota",
'OH'=>"Ohio",  
'OK'=>"Oklahoma",  
'OR'=>"Oregon",  
'PA'=>"Pennsylvania",  
'RI'=>"Rhode Island",  
'SC'=>"South Carolina",  
'SD'=>"South Dakota",
'TN'=>"Tennessee",  
'TX'=>"Texas",  
'UT'=>"Utah",  
'VT'=>"Vermont",  
'VA'=>"Virginia",  
'WA'=>"Washington",  
'WV'=>"West Virginia",  
'WI'=>"Wisconsin",  
'WY'=>"Wyoming"),
//                "IN" => array("Mumbai", "New Delhi", "Bangalore"),
//                "UK" => array("London", "Manchester", "Liverpool")
            );

            // Display city dropdown based on country name
            if($country !== 'Select'){
                echo "<label>State:</label>";
                echo "<select class='form-control' name='state'>";
                foreach($countryArr[$country] as $keys=>$value){
                    
                    
                    $slug = $this->create_slug($keys); 
                    
                    if($selected_state == $slug){
                        $sel = "selected";
                    }
                    else{
                        $sel = "";
                    }
                    echo "<option value=".$slug." ".$sel.">". $value . "</option>";
                    
                    
                    
                }
                echo "</select>";
            } 
        
        
    }
    /**
     * Create slug for title of service
    */
    public function create_slug($string){
       $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
       return strtolower($slug);
    }

    

    public function orderby()
    {
         $sectionid =  $_POST['sectionid'];
          foreach ($sectionid as $key => $getkey) {
            $values = array(
                'order_by'=>$key
            ); 
            $update =  DB::table('taxes')
            ->where('id',$getkey)
            ->update($values);
         }
    }
    
    

    
    
    
    
    

}
