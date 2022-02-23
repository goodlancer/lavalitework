<?php

namespace App\Http\Controllers;
require_once('stripe/init.php');
use Illuminate\Http\Request;
use App\Http\Response\PublicResponse;
use Litepie\Theme\ThemeAndViews;
use Litepie\User\Traits\RoutesAndGuards;
use App\Service;
use App\FrontSection;
use App\TeamMember;
use App\Submission;
use App\MySetting;
use App\EmailInfo;
use App\Property;
use App\Customer;
use Litecms\Contact\Models\Contact;
use Session;
use Illuminate\Support\Facades\DB;
use Log;
use App\Products;
use App\Coupon;
use App\Cart;
use App\Checkout;
use App\Auction;
use App\Sessioncart;
use App\Userpayment;
use App\Simplyaccelerate;
use App\Client;
use App\Paymentsetting;
use App\Wc_email_invoice;
use App\Tax;

class CheckoutController extends Controller
{
    use ThemeAndViews, RoutesAndGuards;
    /**
     * Initialize public controller.
     *
     * @return null
     */
    public function __construct()
    {
        $contact = Contact::first();
        Session::put('phone',$contact->phone);
        $this->response = app(PublicResponse::class);
        $this->setTheme('public');
    }
    /**
     * show homepage
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        //echo "Test";
        return $this->response
        ->layout('home')
        ->view('welcome')
        ->output();
        
    }
    
    public function charge(Request $request)
    {
        require_once('stripe/init.php');
        $stripe_setting = Paymentsetting::where('slug','stripe')->first();
        $client_secret  = $stripe_setting->sandbox_api_password;
        $stripe         = new \Stripe\StripeClient($client_secret);
        
        $customer = $stripe->customers->create([
            'description'     =>'Frontline Bargains',
            'email'           => $request->stripeEmail,
            'source'          => $request->stripeToken
         ]);

         $userid              = user_id();
         $ck_userid           = user_id();
         $users_type          = "customer"; 
         
        if(Session::get('discount_amount')){
            $discount_amount = Session::get('discount_amount');
            $discount_type   = Session::get('discount_type');
            $sumcarts = Cart::where('user_id',$userid)->sum('product_price');
            $discount_amount = $sumcarts*$discount_amount/100;
        }
        else{
            $discount_amount = 0;
        }
        
        if($userid)
        {
            $userid           = user_id();
            $cart_row         = Cart::where('user_id',$userid)->get();
            
        }
        else
        {
           if($request->emailexists == 0)
           {
               
            $s_userid             = $_COOKIE['myuserid_2']; 
            if(Session::get('discount_amount')){
                $discount_amount = Session::get('discount_amount');
                $discount_type   = Session::get('discount_type');
                //$sumcarts = Sessioncart::where('user_id',$s_userid)->sum('product_price');
                 $cart_row = Sessioncart::where('user_id',$userid)->get();
                 foreach($cart_row as $key=>$value)
                 {
                    $product_id                  = $value->product_id;
                    $auction                     = Products::find($product_id);
                    if($auction->saleprice){
                        $price[]          =  $value->no_product * (float)$auction->saleprice;
                    }
                    else{
                        $price[]         =  $value->no_product * (float)$auction->price;   
                    }   
                 }
                 $sumcarts  = array_sum($price);
                
                
                
                
                $discount_amount = $sumcarts*$discount_amount/100;
            }
            else{
                $discount_amount = 0;
            }
                $cart_row             = Sessioncart::where('user_id',$s_userid)->get(); 
                $users_type           = "guests";
                $guest_name           = $request->firstname;
                $guest_email          = $request->email;
                $address_1            = $request->address_first;
                $address_2            = $request->address_second;
                $city                 = $request->city;
                $state                = $request->state;
                $zip                  = $request->zip;
                $userid               = uniqid();
                $guesid_redirect      = "222";
                $created_at           = date('Y-m-d H:i:s');
                $values_guests        = array('name' =>$guest_name,'email' =>$guest_email,'address_1'=>$address_1,'address_2'=>$address_2,'guest_id'=>$userid,'users_type'=>$users_type,'created_at'=>$created_at,'city'=>$city,'state'=>$state,'zipcode'=>$zip);
                DB::table('guests')->insert($values_guests);   
               
               $get_guest_id = DB::table('guests')->where('guest_id',$userid)->first();
               $userid        = $get_guest_id->id;
               
           } 
           else
           {
              $s_userid          = $_COOKIE['myuserid_2'];
              $cart_row          = Sessioncart::where('user_id',$s_userid)->get(); 
              $password          = preg_replace('/[^A-Za-z0-9-]+/', '-', $request->firstname).'@123'; 
              $verifyid          = strtolower(substr($request->firstname,0,2)).time();
              $users_type        = "customer"; 
              $data = [
                    'name'      =>$request->firstname,
                    'email'     =>$request->email,
                    'city'      =>$request->city,
                    'state'     =>$request->state,
                    'zipcode'   =>$request->zip,
                    'password'  =>$password,
                    'api_token' =>str_random(60),
                    'status'    =>'Locked',
                    'user_id'   =>4,
                    'verify_id' =>$verifyid 
               ];
               $user   = Client::create($data);
               $values = array(
                'verify_id'=>$verifyid
               ); 
                DB::table('clients')
                ->where('email',$request->email)
                ->update($values);
                $getid  = Client::where('email',$request->email)->first();
                $userid = $getid->id;
                $firstname =          $request->firstname;
                $useremail =          $request->email; 
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
                    $template_css      =  $wc_email_data[0]->template_css;  
                    
                    $message_body           =  $wc_email_data[0]->message_body;  
                }      
                
                 $img  = url('public/storage/uploads/frontline-logo.png');
                
                
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
        }
           $created_at       = date('Y-m-d h:i:s');
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
            $data              = $request->all();
            $firstname         = $request->firstname;
            $lastname          = $request->lastname;
            $username          = $request->username;
            $paidamount        = $request->paidamount;
            $stripeToken       = $request->stripeToken;
            $stripeTokenType   = $request->stripeTokenType;
            $stripeEmail       = $request->stripeEmail;
            
            $customer_id         = $customer->id;
            $txn_id              = "99".mt_rand(99,999).time();
        
        
            $product_total_price = $product_total_price*100 - $discount_amount*100;
            

            try {
            $charge = $stripe->charges->create([
              'amount'      =>  $product_total_price,
              'customer'      => $customer->id,
              'currency'    => 'usd',
              'description' => 'Frontline Bargains'
            ]);
            $data = "completed";
            }catch(\Stripe\Exception\CardException $e) {
            echo 'Status is:' . $e->getHttpStatus() . '\n';
            echo 'Type is:' . $e->getError()->type . '\n';
            echo 'Code is:' . $e->getError()->code . '\n';
            echo 'Param is:' . $e->getError()->param . '\n';
            echo 'Message is:' . $e->getError()->message . '\n';
            $data = "failed";
            }catch (\Stripe\Exception\RateLimitException $e) {
            $data = "failed";
            } catch (\Stripe\Exception\InvalidRequestException $e) {
            $data = "failed";
            } catch (\Stripe\Exception\AuthenticationException $e) {
            $data = "failed";
            } catch (\Stripe\Exception\ApiConnectionException $e) {
            $data = "failed";
            } catch (\Stripe\Exception\ApiErrorException $e) {
            $data = "failed";
            } catch (Exception $e) {
            $data = "failed";
            }
           // $product_total_price = $product_total_price*100;
            if($data == "completed")
            {
                  DB::table('userpayments')->insert(
                  [
                   'firstname'         =>$firstname,
                   'lastname'          =>$lastname,
                   'fullname'          =>$username,
                   'payamount'         =>$product_total_price_qnt - $discount_amount,
                   'currency'          =>"USD",
                   'quantity'          =>$product_nos_ser,
                   'stripeToken'       =>$stripeToken,
                   'txn_id'            =>$charge->id, 
                   'userid'            =>$userid, 
                   'product_id'        =>$product_ids_ser, 
                   'created_at'        =>$created_at, 
                   'stripeTokenType'   =>$stripeTokenType, 
                   'stripeEmail'       =>$stripeEmail, 
                   'customer_id'       =>$customer_id,
                   'status'            =>$data,
                   'address_first'     =>$request->address_first,
                   'address_second'    =>$request->address_second,
                   'useremail'         =>$request->email,
                   'phone'             =>$request->phone,
                   'city'              =>$request->city,
                   'state'             =>$request->state,
                   'zip'               =>$request->zip,
                   'new_notification'  =>0,
                   'mode'              =>"Stripe",
                   'users_type'        =>$users_type,
                   'discount_amount'   =>$discount_amount,
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
                
            //Invoice to the customer
            $orders_details = DB::table('userpayments')->where('txn_id',$charge->id)->get();
  
            foreach($orders_details as $keyvalue)
            {
                $datass               =  unserialize($keyvalue->product_id);
                $quantity             =  unserialize($keyvalue->quantity);
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
                
                foreach ($datass as $keyss => $valuess) {
                    $p_data = DB::table('products')->where('id',$valuess)->get();
                    foreach ($p_data as $keysx => $valuesx) {
                         $product_namex[]  =  $valuesx->name;
                         $product_pricex[] =  $valuesx->price;
                    }
                }
            } 
            $product_n_qnt = array_combine($product_namex,$quantity);
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
            $img  = url('public/storage/uploads/frontline-logo.png');
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
                                       <span><span>$</span>".$product_pricex[$x]."</span>     
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
                    <div class='address'>
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
                        <p>Frontline Bargains Team</p>
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
                $delete  = Cart::where('user_id',$userid)->delete();
                $delete  = Sessioncart::where('user_id',$s_userid)->delete();
                
                
                Session::forget('discount_amount');
                Session::forget('code');
                Session::forget('discount_type');
                
                if($guesid_redirect)
                {
                    return redirect('ordersubmit'); 
                }   
                else if($ck_userid)
                {
                    return redirect('ordersubmit'); 
                }
                else
                {
                      
                     return redirect('ordersubmit')->with('message_success','please login'); 
                } 
            }
            else
            {
               return redirect('error');
            }
           
         
    }
    public function addProduct(Request $request)
    {
       $data=$request->all();
       $validatedData=[
           'name'=>'required',
           'description'=>'required',
           'file'=>'required',
           'price'=>'required|regex:/^[0-9]*$/'
       ];
       $customMessages = [     
           'price.regex' => 'The :attribute field should be only numeric value.'
       ]; 
         $this->validate($request, $validatedData , $customMessages);

         $filename=time().".".$request->file->extension();
         $request->file->move(public_path('upload'), $filename);
         $name=$request->name;
         $description=$request->description;
         $price=$request->price;
         Product::create([
             'name'=>$name,
             'description'=>$description,
             'photo'=>$filename,
             'price'=>$price
         ]);
        return redirect('addProduct')->with('msg','Product Added Successfully');
         
     }
    
    
    public function addToCart($productid)
    {
        $session_userid = $_COOKIE['myuserid_2'];
        $userid         = user_id();
        $userid         = "";
        $data['user_id']   = user_id();
        $data['user_type'] = user_type();
        $userid = user_id();
        $created_at       = date('Y-m-d h:i:s');
        $product_data     = Products::find($productid);

        if(!$userid)
        { 

          $session_id    = $session_userid;
          $session_carts = 
          DB::table('sessioncarts')->where('product_id',$productid)->where('user_id',$session_id)->get();
          $flag=0;
          if(!$session_carts->isEmpty())
          {
            foreach($session_carts as $key=>$value)
            {
                $product_id         = $value->product_id;
                $row                = Sessioncart::where('product_id',$product_id)->where('user_id',$session_id)->first();
                $row->no_product    = $row->no_product+1;
                $row->user_id       = $session_id;
                $row->product_id    = $productid;
                $row->product_name  = $product_data->title;
                if($product_data->saleprice){
                    $row->product_price = $product_data->saleprice * ($row->no_product);
                }
                else{
                    $row->product_price = $product_data->price * ($row->no_product);
                }
                $row->save();
                return redirect('cart');
            }
          }
          else
          {
            $created_at         = date('Y-m-d h:i:s');
              
            if($product_data->saleprice){
                $product_price = $product_data->saleprice;
            }
            else{
                $product_price = $product_data->price;
            } 
            
              
            DB::table('sessioncarts')->insert(
            [
                'product_id'      =>$productid,
                'no_product'      =>1,
                'created_at'      =>$created_at,
                'user_id'         =>$session_id,
                'product_name'    =>$product_data->title,
                'product_price'   =>$product_price
            ]
            );
            return redirect('cart');
          }

        }
        else
        {
          $product_row       = Products::find($productid);
          if(empty($product_row))
          {
            echo "product Not Found";
          }
         else
         {
             
        $cart_row= Cart::where('product_id',$productid)->where('user_id',$userid)->get();
        $flag=0;
        if(!$cart_row->isEmpty())
        {
          foreach($cart_row as $key=>$value)
          {

            $product_id         = $value->product_id;
            $row                = Cart::where('product_id',$product_id)->where('user_id',$userid)->first();
            $row->no_product    = $row->no_product+1;
            $row->user_id       = $userid;
            $row->product_id    = $productid;
            $row->product_name  = $product_data->title;
              
              
            if($product_data->saleprice){
                $row->product_price = $product_data->saleprice * ($row->no_product);
            }
            else{
                $row->product_price = $product_data->price * ($row->no_product);
            }
 
              
            $row->save();
            return redirect()->back()->with('msg','product added to cart successfully !');
          }
        }
        else
        {
            
            
            if($product_data->saleprice){
                $product_price = $product_data->saleprice;
            }
            else{
                $product_price = $product_data->price;  
            } 
            
            
            
            DB::table('carts')->insert(
            [
            'product_id'      =>$productid,
            'no_product'      =>1,
            'created_at'      =>$created_at,
            'user_id'         =>$userid,
            'product_name'    =>$product_data->title,
            'product_price'   =>$product_price
            ]
                
                
          );
          return redirect()->back()->with('msg','product added to cart successfully !'); 
        }
      }  
         }
          
   }
    
    public function addToCartSubmit(Request $request){
        
        
        $quantity  = $request->quantity;
        $productid = $request->productid;
        
        $session_userid = $_COOKIE['myuserid_2'];
        $userid         = user_id();
        $userid         = "";
        $data['user_id']   = user_id();
        $data['user_type'] = user_type();
        $userid = user_id();
        $created_at       = date('Y-m-d h:i:s');
        $product_data     = Products::find($productid);

        if(!$userid)
        { 

          $session_id    = $session_userid;
          $session_carts = 
          DB::table('sessioncarts')->where('product_id',$productid)->where('user_id',$session_id)->get();
          $flag=0;
          if(!$session_carts->isEmpty())
          {
            foreach($session_carts as $key=>$value)
            {
                $product_id         = $value->product_id;
                $row                = Sessioncart::where('product_id',$product_id)->where('user_id',$session_id)->first();
                $row->no_product    = $row->no_product+1;
                $row->user_id       = $session_id;
                $row->product_id    = $productid;
                $row->product_name  = $product_data->title;
                if($product_data->saleprice){
                    $row->product_price = $product_data->saleprice * ($row->no_product);
                }
                else{
                    $row->product_price = $product_data->price * ($row->no_product);
                }
                $row->save();
                return redirect('cart');
            }
          }
          else
          {
            $created_at         = date('Y-m-d h:i:s');
              
            if($product_data->saleprice){
                $product_price = $product_data->saleprice;
            }
            else{
                $product_price = $product_data->price;
            } 
            
              
            DB::table('sessioncarts')->insert(
            [
                'product_id'      =>$productid,
                'no_product'      =>$quantity,
                'created_at'      =>$created_at,
                'user_id'         =>$session_id,
                'product_name'    =>$product_data->title,
                'product_price'   =>$product_price
            ]
            );
            return redirect('cart');
          }

        }
        else
        {
          $product_row       = Products::find($productid);
          if(empty($product_row))
          {
            echo "product Not Found";
          }
         else
         {
             
        $cart_row= Cart::where('product_id',$productid)->where('user_id',$userid)->get();
        $flag=0;
        if(!$cart_row->isEmpty())
        {
          foreach($cart_row as $key=>$value)
          {

            $product_id         = $value->product_id;
            $row                = Cart::where('product_id',$product_id)->where('user_id',$userid)->first();
            $row->no_product    = $row->no_product+1;
            $row->user_id       = $userid;
            $row->product_id    = $productid;
            $row->product_name  = $product_data->title;
              
              
            if($product_data->saleprice){
                $row->product_price = $product_data->saleprice * ($row->no_product);
            }
            else{
                $row->product_price = $product_data->price * ($row->no_product);
            }
 
              
            $row->save();
            return redirect('cart');
          }
        }
        else
        {
            
            
            if($product_data->saleprice){
                $product_price = $product_data->saleprice;
            }
            else{
                $product_price = $product_data->price;  
            } 
            
            
            DB::table('carts')->insert(
                [
                'product_id'      =>$productid,
                'no_product'      =>$quantity,
                'created_at'      =>$created_at,
                'user_id'         =>$userid,
                'product_name'    =>$product_data->title,
                'product_price'   =>$product_price
                ]     
            );
           return redirect('cart');
        }
      }  
         }
        
        
    }
    
    
    public function cart($removeid="")
    {

        $page          = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('home');
        $sections      = $page->sections;
        $userid        = user_id();
        $session_id    = "";
        $legal_text_in_array= [];
        $session_id    = $_COOKIE['myuserid_2'];
        
        if($userid)
        {
             $carts    = Cart::where('user_id',$userid)->get();
             $cart_row = Cart::where('user_id',$userid)->get();
             foreach($cart_row as $key=>$value)
             {
                $product_id                  = $value->product_id;
                $auction                     = Products::find($product_id);
                if($auction->saleprice){
                    $price[]          =  $value->no_product * (float)$auction->saleprice;
                }
                else{
                    $price[]         =  $value->no_product * (float)$auction->price;   
                }   
             }
             $sumcarts  = array_sum($price);    
        }
        else
        {
             $carts    = Sessioncart::where('user_id',$session_id)->get();
             $cart_row = Sessioncart::where('user_id',$userid)->get();
             foreach($cart_row as $key=>$value)
             {
                $product_id                  = $value->product_id;
                $auction                     = Products::find($product_id);
                if($auction->saleprice){
                    $price[]          =  $value->no_product * (float)$auction->saleprice;
                }
                else{
                    $price[]         =  $value->no_product * (float)$auction->price;   
                }   
             }
             $sumcarts  = array_sum($price);  
            
        } 

    
        if(Session::get('discount_amount')){
             $discount_amount = Session::get('discount_amount');
             $discount_type   = Session::get('discount_type');
             $code            = Session::get('code');
             $discount_amount = $sumcarts*$discount_amount/100;
        }
        else{
                $discount_amount = 0;
        }

        if($carts)
        {
            foreach($carts as $key=>$product_values)
            {
                 $legal_text =  DB::table('products')->select('legal_disclaimer')->where('id',$product_values->product_id)->get();
                 $legal_text_in_array[] = $legal_text[0]->legal_disclaimer;
            }
        }
        if($removeid)
        {
         
            if($userid) 
            {
               $remove_cart = DB::table("carts")->where("id",$removeid)->where('user_id',$userid)->delete();  
            }
            else
            {
               $remove_cart = DB::table("sessioncarts")->where("id",$removeid)->where('user_id',$session_id)->delete();     
            }
            return redirect()->back()->with('msg','Item is removed from cart'); 
        }

        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('cart')
                ->data(compact('page','sections','carts','legal_text_in_array','discount_amount'))
                ->output();
    }

    public function checkout()
    {

        $stripe_setting = Paymentsetting::where('slug','stripe')->first();
        $paypal_setting = Paymentsetting::where('slug','paypal')->first();
        
        $userid        = user_id();
        $session_id    = $_COOKIE['myuserid_2'];

        if($userid)
        {
             $carts    = Cart::where('user_id',$userid)->get();
             $cart_row = Cart::where('user_id',$userid)->get();
             foreach($cart_row as $key=>$value)
             {
                $product_id                  = $value->product_id;
                $auction                     = Products::find($product_id);
                if($auction->saleprice){
                    $price[]          =  $value->no_product * (float)$auction->saleprice;
                }
                else{
                    $price[]         =  $value->no_product * (float)$auction->price;   
                }   
             }
             $sumcarts  = array_sum($price);    
        }
        else
        {
             $carts    = Sessioncart::where('user_id',$session_id)->get();
             $cart_row = Sessioncart::where('user_id',$userid)->get();
             foreach($cart_row as $key=>$value)
             {
                $product_id                  = $value->product_id;
                $auction                     = Products::find($product_id);
                if($auction->saleprice){
                    $price[]          =  $value->no_product * (float)$auction->saleprice;
                }
                else{
                    $price[]         =  $value->no_product * (float)$auction->price;   
                }   
             }
             $sumcarts  = array_sum($price);  
            
        } 


        if(Session::get('discount_amount')){
           
             $discount_amount = Session::get('discount_amount');
             $discount_type   = Session::get('discount_type');
             $discount_amount = $sumcarts*$discount_amount/100;
        }
        else{
  
            $discount_amount = 0;
        }


        if(count($carts) == 0)
        {
          return redirect('cart');
        }

        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('home');
        $sections = $page->sections;
        return  $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('checkout')
                ->data(compact('page', 'sections','carts','stripe_setting','paypal_setting','discount_amount'))
                ->output();
    }

    public function alcarte(Request $request)
    {

        
        $session_id      = $_COOKIE['myuserid_2'];
        $userid          = user_id();
        if($userid){
            $simply_acc_data = $_POST['simply_accelerated'];
            $base_url = url('/');
            $created_at       = date('Y-m-d h:i:s');
            foreach ($simply_acc_data as $key => $value) {
                  $price_Data =  Products::find($key);
                  if($value == "yes")
                  {
                    $cart_row= Cart::where('product_id',$key)->where('user_id',$userid)->get();
                    if(!$cart_row->isEmpty())
                    {

                      $row                = Cart::where('product_id',$key)->where('user_id',$userid)->first();
                      $row->no_product    = 1;
                      $row->user_id       = $userid;
                      $row->product_id    = $key;
                      $row->product_name  = $price_Data->name;
                      $row->product_price = $price_Data->price;
                      $row->save();
                         //return redirect('cart');
                    }
                    else
                    {
                      $flag=0;
                      $price[$key] = $price_Data->price;
                      DB::table('carts')->insert(
                      [
                        'product_id'      =>$key,
                        'no_product'      =>1,
                        'created_at'      =>$created_at,
                        'user_id'         =>$userid,
                        'product_name'    =>$price_Data->name,
                        'product_price'   =>$price_Data->price
                      ]);
                    }
                  }
                  if($value == "no")
                  {
                    $cart_row= Cart::where('product_id',$key)->where('user_id',$userid)->get();
                    if(!$cart_row->isEmpty())
                    {

                      $delete = Cart::where('product_id',$key)->delete();
                    }
                  }

                }
        }
        else
        {
                $simply_acc_data = $_POST['simply_accelerated'];
                $base_url = url('/');
                $created_at       = date('Y-m-d h:i:s');
                foreach ($simply_acc_data as $key => $value) {
                  $price_Data =  Products::find($key);
                   
                  if($value == "yes")
                  {
                    $cart_row = DB::table('sessioncarts')->where('product_id',$key)->where('user_id',$session_id)->get();     
                      
                    if(!$cart_row->isEmpty())
                    {
                      $row                =  Sessioncart::where('product_id',$key)->where('user_id',$session_id)->first();
                      $row->no_product    = 1;
                      $row->user_id       = $session_id;
                      $row->product_id    = $key;
                      $row->product_name  = $price_Data->name;
                      $row->product_price = $price_Data->price;
                      $row->save();
                    }
                    else
                    {
                      DB::table('sessioncarts')->insert(
                      [
                        'product_id'      =>$key,
                        'no_product'      =>1,
                        'created_at'      =>$created_at,
                        'user_id'         =>$session_id,
                        'product_name'    =>$price_Data->name,
                        'product_price'   =>$price_Data->price
                          
                      ]);
                    }
                  }
                  if($value == "no")
                  {
                    $cart_row=  DB::table('sessioncarts')->where('product_id',$key)->where('user_id',$session_id)->get();
                    if(!$cart_row->isEmpty())
                    {
                      $delete = Sessioncart::where('product_id',$key)->delete();
                    } 
                  }

                }

        }
        return redirect('cart'); 
    }
    public function clientprofile(Request $request)
    {
        $user_id = user_id();
        $image = $request->profile_image;
        if (!empty($image)) {
            $folder  = '/uploads/clients/';
            $image   = $request->file('profile_image');
            $filename    = $image->getClientOriginalName();
            $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
            $fname    = substr($withoutExt,0,3).''.time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/storage/uploads/clients/');
            $image->move($destinationPath, $fname);
            $url = url('client/profile');
            $values = array(
                'profile'=>$fname
             ); 
            DB::table('clients')
            ->where('id',$user_id)
            ->update($values);
            return redirect()->away($url);

        }
         return redirect()->back();
  
    }
    public function update_cart_action(Request $request){
        
        
        $product_id = $request->productid;
        $quantiy_id = $request->quantiy;
        
        $userid        = user_id();
        $session_id    = "";
        $legal_text_in_array= [];
        $session_id    = $_COOKIE['myuserid_2'];
        
        foreach($product_id as $keys=>$values){
            
            
        $get_product_saleprice = Products::where('id',$values)->get()[0]->saleprice;  
        $get_product_price     = Products::where('id',$values)->get()[0]->price;
            
            
            
        if($get_product_saleprice){
           $product_price = $get_product_saleprice;
        }
        else{
            $product_price =$get_product_price;
        }
            
        $quanity_update = array(
           'no_product'=>$quantiy_id[$keys],
           'product_price'=>$product_price*$quantiy_id[$keys]
        );
            
        if($userid)
        {
          DB::table('carts')
            ->where('product_id',$values)
            ->update($quanity_update);
        }
        else
        {
            //$carts = Sessioncart::where('user_id',$session_id)->get();
            DB::table('sessioncarts')
            ->where('product_id',$values)->where('user_id',$session_id)
            ->update($quanity_update);
        }       
                
            
        }
         return redirect()->back();
         
    }
    
    
    public function search_zipcode(Request $request){
        
        $zipcode      = $_POST['zipcode'];
        $amount      = $_POST['zipcode'];
        $data_zipcode = Tax::where('zipcode', 'like','%'.$zipcode.'%')->get(); 
        foreach($data_zipcode as $zipkeys=>$zipvals){
            
            $rate = $zipvals->rate;
            //$amount = $amount + $amount%$rate/100; 
           // echo $rate;
            
            if($rate){
                echo $rate;
            }else{
                 echo "false";
            }
            
        }
        
        //die();
     
        
    }
    
    
    
    
    public function applycoupon(){
        
        
        
        $data        =  $_POST['coupon_code'];
        $coupon_code =  $data['code'];
        $coupon_validate  = Coupon::where('code',$coupon_code)->get();
        foreach($coupon_validate as $ckeys=>$cvals){
            $discount_amount =  $cvals->amount;
            $discount_type   =  $cvals->discount_type;
            $min_amount      =  $cvals->min_amount;
            $code            =  $cvals->code;
        }
        $userid        = user_id();
        $session_id    = $_COOKIE['myuserid_2'];
        if($userid)
        {
             $cart_row = Cart::where('user_id',$userid)->get();
             foreach($cart_row as $key=>$value)
             {
                $product_id                  = $value->product_id;
                $auction                     = Products::find($product_id);
                if($auction->saleprice){
                    $price[]          =  $value->no_product * (float)$auction->saleprice;
                }
                else{
                    $price[]         =  $value->no_product * (float)$auction->price;   
                }   
             }
             $sumcarts  = array_sum($price);    
        }
        else
        {
             $sumcarts = Sessioncart::where('user_id',$session_id)->get();
             foreach($cart_row as $key=>$value)
             {
                $product_id                  = $value->product_id;
                $auction                     = Products::find($product_id);
                if($auction->saleprice){
                    $price[]          =  $value->no_product * (float)$auction->saleprice;
                }
                else{
                    $price[]         =  $value->no_product * (float)$auction->price;   
                }   
             }
             $sumcarts  = array_sum($price);    
        } 
        
        
       if($sumcarts >= $min_amount){
            if(count($coupon_validate) > 0){
                Session::put('discount_amount',$discount_amount);
                Session::put('discount_type',$discount_type);
                Session::put('code',$code);
                echo true;
            }
            else{
                echo false;
            }
       }
       else{
           echo false;
       }
       
        
    }

}

?>