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
use App\Location;
use App\Simplyaccelerate;
use App\Submission;
use App\Client;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use Session;
use App\Auction;
class ShopController extends BaseController
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

        $customers_records       = DB::table('clients')->where('status',"Active")->where('order_status','customer')->orderBy('id','asc')->get();
        $customers_records_count = DB::table('clients')->where('status',"Active")->where('order_status','customer')->get()->count();
        $total_pay         = array();
        foreach($customers_records as $key=>$val)
        {
            $total_spend   = 0;
            $userorders    = DB::table('userpayments')->where('userid',$val->id)->get();
            
            foreach($userorders as $keys=>$vals)
            {
                $total_spend = $total_spend + $vals->payamount;    
            }
            
            $total_pay[$val->id]     = $total_spend;
            $success_order[$val->id] = $userorders->count();   
        }
        $menus_title = DB::table('menus')->select('name')->where('slug','customer')->first();
        $menus_title = $menus_title->name;
        return $this->response->setMetaTitle(__('Customers << Shop'))
            ->view('customer.index')
            ->data(compact('customers_records','total_pay','success_order','menus_title','customers_records_count'))
            ->output();
    }
    
    public function guestedit($id)
    {

        $values = array(
            'new_notification'=>1,
        );
        DB::table('userpayments')
            ->where('userid',  $id)
            ->update($values);
        $url = url('admin/guestedit/');
        if(empty($_GET['status'])){
            header("Refresh: 0.001; url=$url/$id?status=1");
        }  
        $orders_details = DB::table('userpayments')->where('userid',$id)->where('users_type','guests')->get();
        foreach($orders_details as $keyvalue)
        {
            $data     =  unserialize($keyvalue->product_id);
            $quantity =  unserialize($keyvalue->quantity);
            $x=0;
            foreach ($data as $key => $value) {
                $p_data = DB::table('products')->where('id',$value)->get();
                foreach ($p_data as $keys => $values) {
                $product_name[]  =  $values->title;
                $product_price[] =  $values->price;
                }
            }
        } 
        $product_n_qnt = array_combine($product_name,$quantity);
        return $this->response->setMetaTitle(__('Guest Customers << Orders Status'))
        ->view('customer.guestedits')
        ->data(compact('orders_details','product_n_qnt','product_price'))
        ->output(); 

    }
    public function guest()
    {
        $customers_records = DB::table('guests')->orderBy('id','desc')->get();
        
        $customers_records_cnt = DB::table('guests')->get()->count();
        
        
        $total_pay     = array();
        foreach($customers_records as $key=>$val)
        {

            $total_spend   = 0;
            $userorders    = DB::table('userpayments')->where('userid',$val->id)->where('users_type','guests')->get();

            foreach($userorders as $keys=>$vals)
            {
                $total_spend = $total_spend + $vals->payamount;    
                $mode = $vals->mode;    
                $new_notification[$vals->userid]         = $vals->new_notification;
            }
            
            $total_pay[$val->id]                    = $total_spend;
            $success_order[$val->id]                = $userorders->count();
            
            $modes[$val->id]                        = $mode;
            
           
            
        }
        

        
        $menus_title = DB::table('menus')->select('name')->where('slug','woo-commerce-guest')->first();
        $menus_title = $menus_title->name;
        
        return $this->response->setMetaTitle(__($menus_title))
            ->view('customer.guests')
            ->data(compact('customers_records','total_pay','success_order','modes','menus_title','customers_records_cnt','new_notification'))
            ->output();

    }
    public function edit($id)
    {
        $data = DB::table('clients')->where('id',$id)->first();
        return $this->response->setMetaTitle("Customer Edit")
        ->view('customer.edit')
        ->data(compact('data'))
        ->output();
    }
    public function orders()
    {
        $users_orders = DB::table('userpayments')->orderby('id','desc')->get();
        $users_orders_cnt = DB::table('userpayments')->orderby('id','desc')->get()->count();
        $menus_title = DB::table('menus')->select('name')->where('slug','orders')->first();
        $menus_title = $menus_title->name;
        foreach($users_orders as $keyvalue)
        {
            $data     =  unserialize($keyvalue->product_id);
            $quantity =  unserialize($keyvalue->quantity);
            $x=0;

            foreach ($data as $key => $value) {

                $p_data = DB::table('products')->where('id',$value)->get();
                foreach ($p_data as $keys => $values) {
                     $product_name[]  =  $values->name;
                     $product_price[] =  $values->price;
                }
            }

            $product_serialize[$keyvalue->id] = serialize($product_name);
            $product_price_serialize[$keyvalue->id] = serialize($product_price);

            unset($product_name);
            unset($product_price);
        } 
        return $this->response->setMetaTitle(__('Customers << Orders'))
        ->view('customer.orders')
        ->data(compact('users_orders','menus_title','users_orders_cnt','product_serialize','product_price_serialize'))
        ->output();


    }
    public function order_status($id="")
    {
        $values = array(
            'new_notification'=>1,
        );
        
        DB::table('userpayments')
        ->where('id',  $id)
        ->update($values);
        $url = url('admin/order_status');
        
        if(empty($_GET['status'])){
            header("Refresh: 0.001; url=$url/$id?status=1");
        }  
        
        $orders_details = DB::table('userpayments')->where('id',$id)->get();
        foreach($orders_details as $keyvalue)
        {
            $data     =  unserialize($keyvalue->product_id);
            $quantity =  unserialize($keyvalue->quantity);
             $x=0;
            foreach ($data as $key => $value) {
               
                $p_data = DB::table('products')->where('id',$value)->get();
                foreach ($p_data as $keys => $values) {
                     $product_name[]  =  $values->title;
                    
                     if($values->saleprice){
                        $product_price[]          =  (float)$values->saleprice;
                    }
                    else{
                        $product_price[]         =   (float)$values->price;   
                    }   
                     
                }
            }
        } 
        $product_n_qnt = array_combine($product_name,$quantity);
        return $this->response->setMetaTitle(__('Customers << Orders Status'))
            ->view('customer.orderstatus')
            ->data(compact('orders_details','product_n_qnt','product_price'))
            ->output();
        

    }
    public function customerupdate(Request $request)
    {
        $clients                              = Client::find($request->id);
        $clients->name                        = $request->name;
        $clients->sex                         = $request->sex;
        $clients->mobile                      = $request->mobile;
        $clients->phone                       = $request->phone;
        $clients->address                     = $request->address;
        $clients->street                      = $request->street;
        $clients->city                        = $request->city;
        $clients->country                     = $request->country;
        $clients->web                         = $request->web;
        $clients->zipcode                     = $request->zipcode;
        
        if($request->password)
        {
           $clients->password                 = $request->password; 
        }
        
        $clients->created_at                  = date('Y-m-d');
        $clients->updated_at                  = date('Y-m-d');
        $clients->save();
        
        return redirect()->back();
        
    }
    public function update(Request $request)
    {
        try {  
            
            $order_status     = $request->order_status;
            $created_at       = $request->created_at;
            $hrs              = $request->hrs;
            $mins             = $request->mins;
            $date  = date_create($created_at);
            $date_f = date_format($date,"Y-m-d");
            $updated_at = date('Y-m-d H:i:s', strtotime("".$date_f." ".$hrs.":".$mins.""));

            $values = array(
            'status'      => $order_status,
            'created_at'   =>$updated_at
            );
            DB::table('userpayments')
            ->where('id',  $request->id)
            ->update($values);
            return redirect()->back()->with('msg',"Updated Successfully");
        } 
        catch (Exception $e) {
        return $this->response->message($e->getMessage())
        ->code(400)
        ->status('error')
        ->url(guard_url('facility/'))
        ->redirect();
        }

    }
    public function wooguests_deletes()
    {
        $row_id =  $_POST['row_id'];
        $delete = DB::table('guests')
        ->where('id',$row_id)->delete();
        
        if($delete)
        {
            echo "true";
        }
    }
    public function customer_deletes()
    {
        $row_id =  $_POST['row_id'];
        $delete = DB::table('clients')
        ->where('id',$row_id)->delete();
        
        if($delete)
        {
            echo "true";
        } 
    }
    public function orders_deletes()
    {
        $row_id = $_POST['row_id'];
        $delete = DB::table('userpayments')
        ->where('id',$row_id)->delete();
        
        if($delete)
        {
            echo "true";
        } 
   }  
}
