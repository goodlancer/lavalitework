<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Response\PublicResponse;
use Litepie\Theme\ThemeAndViews;
use Litepie\User\Traits\RoutesAndGuards;;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;

/** All Paypal Details class **/
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Redirect;
use Session;
use URL;
use App\Products;
use App\Cart;
use App\Auction;
use App\Sessioncart;
use App\Userpayment;
use App\Simplyaccelerate;
use App\Client;
use App\Paymentsetting;
use App\Wc_email_invoice;
use App\Tax;
class PayPalController extends Controller
{
    use ThemeAndViews, RoutesAndGuards;
    private $_api_context;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
 
    	$this->response = app(PublicResponse::class);
        $this->setTheme('public');

        /** PayPal api context **/
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_conf['client_id'],
            $paypal_conf['secret'])
        );
        $this->_api_context->setConfig($paypal_conf['settings']);

    }
    public function index()
    {

        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('home');
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('paywithpaypal')
            ->data(compact('page'))
            ->output();

      
    }
    public function payWithpaypal(Request $request)
    {
                

        $firstname         = $request->firstname;
        $lastname          = $request->lastname;
        $username          = $request->username;
        $paidamount        = $request->paidamount;  
        $zipcode           = $request->zip;
        
        
        $data_zipcode = Tax::where('zipcode', 'like','%'.$zipcode.'%')->get(); 
        foreach($data_zipcode as $zipkeys=>$zipvals){
            $rates = $zipvals->rate;  
        }
        
        
        $useremail         = $request->email;
        $created_at        = date('Y-m-d h:i:s');
        $userid            = user_id(); 
        
        if($userid)
        {
            $userid = user_id(); 
            $cart_row          = Cart::where('user_id',$userid)->get();
            if(!$cart_row->isEmpty())
            {
                foreach($cart_row as $key=>$value)
                {
                    $product_id                  = $value->product_id;
                    $auction                     = Products::find($product_id); 
                    if($auction->saleprice){
                        $product_price   =  $value->no_product * $auction->saleprice;
                    }
                    else{
                        $product_price =  $value->no_product * $auction->price;   
                    } 
                    $each_no_product_price[]     = $product_price;
                    $product_ids[]               = $value->product_id;
                    $product_nos[]               = $value->no_product;
                    $product_nos_name[]          = $auction->name;
                }
                $product_total_price                       = array_sum($each_no_product_price);
                $product_total_price_qnt                   = array_sum($each_no_product_price);
                $product_ids_ser                           = serialize($product_ids);
                $product_nos_ser                           = serialize($product_nos);
                $product_nos_name_ser                      = serialize($product_nos_name);
                $sumcarts = Cart::where('user_id',$userid)->sum('product_price');
                if(Session::get('discount_amount')){
                    $discount_amount = Session::get('discount_amount');
                    $discount_type   = Session::get('discount_type');
                    $discount_amount = $sumcarts*$discount_amount/100;
                }
                else{
                   $discount_amount = 0;
                }
                
                $product_total_price_qnt = $product_total_price_qnt - $discount_amount;
                $product_total_price_qnt  = $product_total_price_qnt + ($product_total_price_qnt/100)*$rates;
                
            }       
        }
        else
        {
            $userid            = $_COOKIE['myuserid_2'];
            $cart_row          = Sessioncart::where('user_id',$userid)->get(); 
            if(!$cart_row->isEmpty())
            {
                foreach($cart_row as $key=>$value)
                {
                    $product_id                  = $value->product_id;
                    $auction                     = Products::find($product_id);
                    
                    
                    if($auction->saleprice){
                        $product_price   =  $value->no_product * $auction->saleprice;
                    }
                    else{
                        $product_price =  $value->no_product * $auction->price;   
                    } 
                    
                    
                    $each_no_product_price[]     = $product_price;
                    $product_ids[]               = $value->product_id;
                    $product_nos[]               = $value->no_product;
                    $product_nos_name[]          = $auction->name;
                }
                $product_total_price                       = array_sum($each_no_product_price);
                $product_total_price_qnt                   = array_sum($each_no_product_price);
                $product_ids_ser                           = serialize($product_ids);
                $product_nos_ser                           = serialize($product_nos);
                $product_nos_name_ser                      = serialize($product_nos_name);
            }  
                 $sumcarts = Sessioncart::where('user_id',$userid)->sum('product_price');
                if(Session::get('discount_amount')){
                    $discount_amount = Session::get('discount_amount');
                    $discount_type   = Session::get('discount_type');
                    $discount_amount = $sumcarts*$discount_amount/100;
                }
                else{
                   $discount_amount = 0;
                }
            
            $product_total_price_qnt = $product_total_price_qnt - $discount_amount;
            
            
            $product_total_price_qnt  = $product_total_price_qnt + ($product_total_price_qnt/100)*$rates;
       
            $password      = preg_replace('/[^A-Za-z0-9-]+/', '-', $firstname).'@123'; 
            $verifyid      = strtolower(substr($firstname,0,2)).time();
            $users_type    = "customer";
            
            if($request->emailexists == 1){
              $data = [
                    'name'      =>$firstname,
                    'email'     =>$request->email,
                    'password'  =>$password,
                    'api_token' =>str_random(60),
                    'city'      =>$request->city,
                    'state'     =>$request->state,
                    'zipcode'   =>$request->zip,
                    'status'    =>'Locked',
                    'user_id'   =>4,
                    'verify_id' =>$verifyid 
                ];
                $user  = Client::create($data);
                $values = array(
                    'verify_id'=>$verifyid
                ); 
                DB::table('clients')
                ->where('email',$request->email)
                ->update($values);
                $getid = Client::where('email',$request->email)->first();
                $userid = $getid->id;
                Session::put('new_user_id', $userid);
                $users_type = "customer"; 
                
          
                
                  //Order New Account controller
            $wc_email_data = Wc_email_invoice::where('slug','new-account-3')->get();
            if(count($wc_email_data) > 0)
            {
                $subject                =  $wc_email_data[0]->acc_subject;
                $from                   =  $wc_email_data[0]->acc_from_email;
                $fromName               =  $wc_email_data[0]->acc_subject;
                $emailSubject           =  $wc_email_data[0]->acc_subject;
                $email                  =  $wc_email_data[0]->acc_from_email;
                $acc_email_heading      =  $wc_email_data[0]->acc_email_heading;  
                $template_css           =  $wc_email_data[0]->template_css;  
                
                $message_body           =  $wc_email_data[0]->message_body;  
            }       
            $img                    =  url('public/themes/admin/assets/img/logo/redsuit-logo.png');
            $url                    =  url('/verifyemail/').'/'.$verifyid; 
           //admin    
            $a = array('email_heading'=>$acc_email_heading,'url'=>$url,'us_email'=>$useremail,'userspassword'=>$password);   
            $htmlbody = preg_replace_callback('~\{(.*?)\}~',
            function($key) use($a)
            {
            $variable['emailheading']   = $a['email_heading'];
            $variable['usersemail']     = $a['us_email'];
            $variable['userspassword']  = $a['userspassword'];
            $variable['urlapprove']     = $a['url'];
            return $variable[$key[1]];
            },
           $message_body); 
           $htmlContent = "<html>
            <head>
            <title>Please Follow the Instruction</title>
            ".$template_css."
            </head>
            <body>
                ".$htmlbody."
            </body>
            </html>"; 
                
            $headers = "From: $fromName"." <".$from.">";
            $semi_rand = md5(time()); 
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
            "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n"; 
            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . $email;
            $send =  mail($useremail, $emailSubject, $message, $headers, $returnpath);  
                
            }
            if($request->emailexists == 0){
                $users_type           = "guests";
                $guest_name           = $request->firstname;
                $guest_email          = $request->email;
                $address_1            = $request->address_first;
                $address_2            = $request->address_second;
                $guest_name_trim      = str_replace(' ', '', $guest_name);
                $trim_name            = substr($guest_name_trim,0,3);
                $guest_id             = uniqid();
                $created_at           = date('Y-m-d H:i:s');
                $city                 = $request->city;
                $state                = $request->state;
                $zip                  = $request->zip;
                $values_guests        = array('name' =>$guest_name,'email' =>$guest_email,'address_1'=>$address_1,'address_2'=>$address_2,'guest_id'=>$guest_id,'created_at'=>$created_at,'city'=>$city,'state'=>$state,'zipcode'=>$zip);
                DB::table('guests')->insert($values_guests);  
                $get_guest_id = DB::table('guests')->where('guest_id',$guest_id)->first();
                $gu_id        = $get_guest_id->id;
             }
            
            
           
        }

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $item_1 = new Item();
        $item_1->setName('Item 1') /** item name **/
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice($request->get('amount')); /** unit price **/
        $item_list = new ItemList();
        $item_list->setItems(array($item_1));
        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal($request->get('amount'));
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Your transaction description');
        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::to('status')) /** Specify return URL **/
            ->setCancelUrl(URL::to('status'));
        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        try {
                  if($request->emailexists == 0 and $request->emailexists != null ){
                       $userid = $gu_id;
                       Session::put('s_guest_id', $gu_id );  
                  }
                  $payment->create($this->_api_context);
                  DB::table('userpayments')->insert(
                  [
                   'firstname'         =>$firstname,
                   'lastname'          =>$lastname,
                   'fullname'          =>$username,
                   'payamount'         =>$product_total_price_qnt,
                   'currency'          =>"USD",
                   'quantity'          =>$product_nos_ser,
                   'stripeToken'       =>"",
                   'txn_id'            =>"", 
                   'userid'            =>$userid, 
                   'product_id'        =>$product_ids_ser, 
                   'created_at'        =>$created_at, 
                   'stripeTokenType'   =>"", 
                   'stripeEmail'       =>"", 
                   'customer_id'       =>"",
                   'status'            =>"pending",
                   'users_type'        =>$users_type,
                   'address_first'     =>$request->address_first,
                   'address_second'    =>$request->address_second,
                   'useremail'         =>$request->email,
                   'phone'             =>$request->phone,
                   'city'              =>$request->city,
                   'state'             =>$request->state,
                   'zip'               =>$request->zip,
                   'new_notification'  =>0,
                   'mode'              =>"PayPal",
                   'payer_id'          =>$payment->getId(),
                   'discount_amount'   =>$discount_amount,
                   'tax'               =>$rates,
                   'apply_coupon_code' =>Session::get('code'),
                   'ip'                =>$_SERVER['REMOTE_ADDR']
                  ]
                );
                $values = 
                array(
                    'order_status'=>"customer",
                ); 
                DB::table('clients')
                ->where('id',$userid)
                ->update($values); 

        } catch (\PayPal\Exception\PPConnectionException $ex) {

            if (\Config::get('app.debug')) {

                \Session::put('error', 'Connection timeout');
                return Redirect::to('/');

            } else {

                \Session::put('error', 'Some error occur, sorry for inconvenient');
                return Redirect::to('/');

            }

        }
        foreach ($payment->getLinks() as $link) {

            if ($link->getRel() == 'approval_url') {

                $redirect_url = $link->getHref();
                break;

            }

        }
        Session::put('paypal_payment_id', $payment->getId());

        if (isset($redirect_url)) {
            return Redirect::away($redirect_url);
        }

        \Session::put('error', 'Unknown error occurred');
        return Redirect::to('/');

    }

    public function getPaymentStatus()
    {
        $userid = user_id(); 
        if($userid)
        {
             $userid = user_id(); 
        }
        else
        {
              $session_g_id         = Session::get('s_guest_id');
              $session_id           = $_COOKIE['myuserid_2'];
        }
        if(Session::get('new_user_id'))
        {
              $new_user_id        = Session::get('new_user_id');
        }
        $payment_id            = Session::get('paypal_payment_id');
        $payment   = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution(); 
        $execution->setPayerId($_GET['PayerID']);
        $result = $payment->execute($execution, $this->_api_context);
        $payment_time   = $payment->create_time;
        $payer_email    = $payment->payer->payer_info->email;
        $first_name     = $payment->payer->payer_info->first_name;
        $country_code   = $payment->payer->payer_info->shipping_address->country_code;
        $txnid="";
        $amout_currency="";
        foreach ($payment->transactions as $key => $tvalue) {
            $amount_total   = $tvalue->amount->total;
            $amout_currency = $tvalue->amount->currency;
            foreach ($tvalue->related_resources as $keys => $txnvalue) {
                $txnid = $txnvalue->sale->id;
            }
        }
        if ($result->getState() == 'approved') {

           //Invoice to the customer
            $orders_details = DB::table('userpayments')->where('payer_id',$payment_id)->get();
            foreach($orders_details as $keyvalue)
            {
                $data     =  unserialize($keyvalue->product_id);
                $quantity =  unserialize($keyvalue->quantity);
                $payamount            =  $keyvalue->payamount;
                $paymode              =  $keyvalue->mode;
                $firstname            =  $keyvalue->firstname;
                $lastname             =  $keyvalue->lastname;
                $address_first        =  $keyvalue->address_first;
                $address_second       =  $keyvalue->address_second;
                $phone                =  $keyvalue->phone;
                $city                 =  $keyvalue->city;
                $state                =  $keyvalue->state;
                $zip                  =  $keyvalue->zip;
                $useremails           =  $keyvalue->useremail;
                $order_id             =  $keyvalue->id;
                $created_at           =  $keyvalue->created_at;
                $created_at           =  date_create($created_at);

                $created_at           =  date_format($created_at,"F d, Y @ H:i");
                $x=0;
                
                foreach ($data as $key => $value) {
                    $p_data = DB::table('products')->where('id',$value)->get();
                    foreach ($p_data as $keys => $values) {
                         $product_name[]  =  $values->name;
                         $product_price[] =  $values->price;
                    }
                }
            } 
            $product_n_qnt = array_combine($product_name,$quantity);
            
            
            
            
            //Order Emails controller
            $wc_email_data =  Wc_email_invoice::where('slug','default')->get();
            
            if(count($wc_email_data) > 0)
            {
                $subject                =  $wc_email_data[0]->acc_subject;
                $from                   =  $wc_email_data[0]->acc_from_email;
                $acc_email_heading      =  $wc_email_data[0]->acc_email_heading;
                $fromName               =  $wc_email_data[0]->acc_subject;
                $emailSubject           =  $wc_email_data[0]->acc_subject;
                $email                  =  $wc_email_data[0]->acc_from_email;
                $template_css           =  $wc_email_data[0]->template_css;  
                $message_body           =  $wc_email_data[0]->message_body;  
            }   
            $img  = url('public/storage/uploads/red-logo.png');
            
            //Admin Things
                $a = array('email_heading'=>$acc_email_heading,'url'=>$img,'firstname'=>$firstname,'order_id'=>$order_id,'created_at'=>$created_at);  
            
                $htmlbody = preg_replace_callback('~\{(.*?)\}~',
                function($key) use($a)
                {
                    $variable['email_heading']   = $a['email_heading'];
                    $variable['firstname']       = $a['firstname'];
                    $variable['order_id']        = $a['order_id'];
                    $variable['created_at']      = $a['created_at'];
                    $variable['img']             = $a['url'];
                    return $variable[$key[1]];
                },
                $message_body);     
            //Admin Things           
            $htmlContent  = "";
            $htmlContent .= "<html>
                            <head>
                            <title>Please Follow the Instruction</title>
                            $template_css
                            <body>
                            $htmlbody
                            ";
                            $x = 0;
                            foreach ($product_n_qnt as $key => $value)
                            {
                              $htmlContent .= "<tr>
                                    <td style='color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;'>
                                        ".$key."
                                     </td>
                                    <td style='color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;'>
                                       ".$value."           
                                    </td>
                                    <td style='color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;'>
                                       <span><span>$</span>".$product_price[$x]."</span>     
                                    </td>
                               </tr>";
                            }
                            $htmlContent .= "
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th scope='row' colspan='2' style='color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px'>Subtotal:</th>
                                    <td style='color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px'><span><span>$</span>".$payamount."</span></td>
                                </tr>
                                <tr>
                                    <th scope='row' colspan='2' style='color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left'>Payment method:</th>
                                    <td style='color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left'>".$paymode."</td>
                                </tr>
                                <tr>
                                    <th scope='row' colspan='2' style='color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left'>Total:</th>
                                    <td style='color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left'><span><span>$</span>".$payamount."</span></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class='address' style='width:100%;'>
                        <address style='padding:12px;color:#636363;border:1px solid #e5e5e5'>
                               ".$firstname." ".$lastname."
                               <br>".$address_first."<br>
                                 ".$address_second."<br>
                               ".$city.", ".$state." ".$zip."                                
                               <br>".$phone."                                                 
                               <br>
                              <a href='mailto:".$useremail."' target='_blank'>".$useremail."</a>                         
                         </address>
                    </div>
                    
                    <div class='footer' style='margin-top:30px;'>
                        <strong>Thanks!</strong>
                        <p>FrontlineBergains Team</p>
                    </div>
                    
                </div>
            </div>
            </body>
            </html>";
            
            
            $headers = "From: $fromName"." <".$from.">";
            $semi_rand = md5(time()); 
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
            "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n"; 
            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . $email;
            $send =  mail($useremails, $emailSubject, $message, $headers, $returnpath);      
           //Invoice to the customer 
            
            
            
            $values = array(
                'status'       =>"completed",
                'token_id'     =>$_GET['token'],
                'txn_id'       =>$txnid,
                'stripeEmail'  =>$payer_email,
                'account_name' =>"Krishna Kumar Raut",
                'currency'     =>$amout_currency
            ); 
            if($userid)
            {
                DB::table('userpayments')
                ->where('userid',$userid)->where('payer_id',$payment_id)
                ->update($values); 
                $delete  = Cart::where('user_id',$userid)->delete(); 
                Session::forget('discount_amount');
                Session::forget('code');
                Session::forget('discount_type');
                Session::forget('paypal_payment_id');
                Session::forget('new_user_id');
                return Redirect::to('/ordersubmit');
            }
            else
            {
                if($new_user_id)
                {
                    DB::table('userpayments')
                    ->where('userid',$new_user_id)->where('payer_id',$payment_id)
                    ->update($values);
                }
                else
                {
                     DB::table('userpayments')
                    ->where('userid',$session_g_id)->where('payer_id',$payment_id)
                    ->update($values);
                }
                $delete  = Sessioncart::where('user_id',$session_id)->delete(); 
                Session::forget('paypal_payment_id');
                Session::forget('new_user_id');
                Session::forget('s_guest_id');
                Session::forget('discount_amount');
                Session::forget('code');
                Session::forget('discount_type');
                
                return Redirect::to('/ordersubmit')->with('message_success', 'Credentials is send to your email');
            }    
        }
        \Session::put('error', 'Payment failed');
        return Redirect::to('/paypal');

    }

}