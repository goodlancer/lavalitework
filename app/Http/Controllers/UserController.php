<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Litepie\Theme\ThemeAndViews;
use Litepie\User\Traits\RoutesAndGuards;
use Litepie\User\Traits\UserPages;
use App\Http\Response\ResourceResponse;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    use RoutesAndGuards, ThemeAndViews, UserPages;

    /**
     * Initialize public controller.
     *
     * @return null
     */
    public function __construct()
    {
        guard(request()->guard . '.web');
        $this->middleware('auth:' . guard());
        $this->response = app(ResourceResponse::class);
        $this->setTheme();
    }

    /**
     * Show dashboard for each user.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        $userid        = user_id();

        $userorders    = DB::table('userpayments')->where('userid',$userid)->where('status','completed')->get();
        $success_order = $userorders->count();
       
        return $this->response
            ->setMetaTitle(strip_tags("Dashboard"))
            ->layout('user')
            ->title('Dashboard')
            ->view('home')
            ->data(compact('userorders','success_order'))
            ->output();
    }
    public function orders()
    {

        $userid       = user_id();
        $users_orders = DB::table('userpayments')->where('userid',$userid)->get();


        return 
        $this->response
        ->setMetaTitle(strip_tags("orders"))
        ->layout('user')
        ->title('Dashboard')
        ->view('orders')
        ->data(compact('users_orders'))
        ->output();
    }
    
    public function order_details($id="")
    {
      
        $userid         = user_id();
        $orders_details = DB::table('userpayments')->where('userid',$userid)->where('id',$id)->get();
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

        return 
        $this->response
        ->setMetaTitle(strip_tags("orders"))
        ->layout('user')
        ->title('Dashboard')
        ->data(compact('orders_details','product_n_qnt','product_price'))
        ->view('orderdetails')
        ->output();
    }

}
