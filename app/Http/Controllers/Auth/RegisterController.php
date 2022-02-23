<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Response\Auth\Response as AuthResponse;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Litepie\Theme\ThemeAndViews;
use Litepie\User\Traits\Auth\RegistersUsers;
use Litepie\User\Traits\RoutesAndGuards;
use App\Client;
use Illuminate\Support\Facades\DB;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers, RoutesAndGuards, ThemeAndViews;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $guard = request()->guard;
        guard($guard . '.web');
        $this->response   = resolve(AuthResponse::class);
        $this->redirectTo = '/'.$guard;
        $this->middleware('guest');
        $this->setTheme();
    }
    protected function Register(Request $request)
    {

        $data      = $request->all();
        $user      = Client::where('email', '=', $data['email'])->first();
        $username  = $data['name'];
        $useremail = $data['email'];
        
        $verifyid      = strtolower(substr($username,0,2)).time();
        

        
        if($user ===  null  )
        {
           $data = [
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => $data['password'],
                'api_token' => str_random(60),
                'status'    =>'Locked',
                'user_id'   =>4,
                'verify_id' =>$verifyid 
           ];
            
           $user  = Client::create($data);
            
            
            $values = array(
                'verify_id'=>$verifyid
            ); 
            
            DB::table('clients')
            ->where('email',$data['email'])
            ->update($values);
            
//            
            
            
//Email Approve
            
            $subject                =  "OwenGraffix Demo Site";
            $from                   =  "demo@owengraffix.com";
            $fromName               =  "Owengraffix";
            $emailSubject           =  "Owengraffix Site";
            $email                  =  "developer.owengraffix@gmail.com";
            $img                    =  url('public/themes/public/assets/img/1609152716.png');  
            $url                    =  url('/verifyemail/').'/'.$verifyid;
                  //admin  
            $htmlContent = "<html>
            <head>
            <title>Please Follow the Instruction</title>
              <style type='text/css'>
              .content-fixed
              {
                width:600px;         
              }
              .wrap-image
              {
                  text-align:center;
                  padding:15px;
              }
              img
              {
                  max-width:150px;
                  max-height:150px;
              }
              .content{           
                  padding:10px;
              }
              .link_verify
              {
                background:#f7be0c;
                padding:10px 15px;
                border-radius:50px;
                color:#fff;
                font-size:18px;
                font-weight:bold;
              }
              </style>
            </head>
            <body>
            <div class='content-fixed'>
                 <div class='wrap-image'>
                   <img src='$img'>
                 </div>
                 <div class='content'>
                    <p>Username: <strong>$username</strong></p>
                    <p>Email:    <strong>$useremail</strong></p>
                    <a href='$url' class='link_verify'>Verify email address</a>
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
            $send =  mail($useremail, $emailSubject, $message, $headers, $returnpath);
            
            
            if($send)
            {
                return redirect('client/login')->with("msg","Please verify email to activate your account!!"); 
                
            }
            else
            {
               return redirect('client/register'); 
            }
            
            
           
        }
        else
        {
             return redirect()->back()->with('error','Email Already Exists!!!');  
        }

        
    }
}
