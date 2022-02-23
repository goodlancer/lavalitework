<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;

use App\Http\Response\PublicResponse;

use Litepie\Theme\ThemeAndViews;
use Litepie\User\Traits\RoutesAndGuards;


use Litepie\User\Traits\UserPages;
use Illuminate\Support\Facades\Input;
use App\Traits\UploadTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Litepie\Settings\Models\Setting;
use Log;
use Session;
use App\Client;


class PasswordController extends BaseController
{
    
    use ThemeAndViews, RoutesAndGuards;
    
    /**
     * Initialize public controller.
     *
     * @return null
     */
    
    public function __construct()
    {
        $this->response = app(PublicResponse::class);
        $this->setTheme('public');
    }
    
    
    public function getemails()
    {
       
        
        $email = $_POST['email'];
        $data  = DB::table('users')->where('email',$email)->first();
        $verify_id = substr($email,0,3).''.time();
        if($data)
        {
            
            $values = array(
                'verifyid'=>$verify_id,
            );
            $uname     =  $data->name;
            
            $useremail = $email;
            DB::table('users')
            ->where('email',$email)
            ->update($values);

            $subject                =  "Banner Drug";
            $from                   =  "info@owengraffix.com";
            $fromName               =  "Banner Drug";
            $emailSubject           =  "Banner Drug";
            
            $email                  =  "info.owengraffix@gmail.com";
            
            $img                    =  url('public/themes/public/assets/img/banner-drug-logo.jpg');
            
            $url                    =  url('/update_password/').'/'.$verify_id;
                
                  //admin  
            $htmlContent = "<html>
            <head>
            <title>Please Follow the Instruction</title>
              <style type='text/css'>
              body p
              {
                color:#000;
              }
              .content-fixed
              {
                width:600px;         
              }
              .wrap-image
              {
                  background:#fff;
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
                margin-top:30px;
                color:#000!important;
                font-size:15px;
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
                    <p>Username:<strong>$uname</strong></p>
                
                    <p>Note:Please click to below link to update password.</p>
                    <p><a href='$url' class='link_verify'>Update Password</a></p>
                    
                    <p>Banner Drug Team.</p>
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
            echo "true";
        }
        else
        {
            echo "false";
        }


       
    }

    
    public function usersupdatepasswords($slugid="")
    { 
        return $this->response
            ->setMetaKeyword('Login')
            ->setMetaDescription('Login')
            ->setMetaTitle('Login')
            ->layout('home')
            ->view('updatepassword')
            ->data(compact('slugid'))
            ->output();
    }
    public function admin_updatepasword()
    {
        $password = Hash::make($_POST['password']);
        $verify   = $_POST['verifyid'];
        
        $values = array(
           'password'=>$password,
        );
        
        $update = DB::table('users')->where('verifyid',$verify)->update($values);
        
        if($update)
        {
            echo "true";
        }
        else
        {
            echo "false";
        }
    
    }
    
    public function logins()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enlogin');
        $userid = user_id(); 
        if($userid)
        {
          return redirect('client');  
        }      
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('login')
            ->output();
    }
    
    public function forgot_password()
    {
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enforgot-password');
        
        $userid = user_id(); 
        if($userid)
        {
          return redirect('client');  
        }
        return $this->response
                ->setMetaKeyword(strip_tags($page->meta_keyword))
                ->setMetaDescription(strip_tags($page->meta_description))
                ->setMetaTitle(strip_tags($page->meta_title))
                ->layout('home')
                ->view('forgot')
                ->output();
    }
    public function forgotemails()
    {
        
      
        $email = $_POST['email'];
        $data  = DB::table('clients')->where('email',$email)->first();
        if($data)
        {

            $verify_id =  $data->verify_id;
            $uname     =  $data->name;
            $useremail  = $email;

            $subject                =  "OG-CMS - Update Password";
            $from                   =  "info@owengraffix.com";
            $fromName               =  "OG-CMS";
            $emailSubject           =  "OG-CMS";
            $email                  =  "info.owengraffix.com";
            
          //  $img                    =  url('public/themes/admin/assets/img/oglogo-white-y.jpg');
            
            $img                    =  url('public/themes/public/assets/img/oglogo-white-y.jpg');
            
            $url                    =  url('/customer_update_password/').'/'.$verify_id.'?id='.uniqid();
            

        
       
            //admin 
            $htmlContent = "<html>
            <head>
            <title>Please Follow the Instruction</title>
              <style type='text/css'>
              body p
              {
                color:#000;
              }
              .content-fixed
              {
                width:600px;         
              }
              .wrap-image
              {
                  background:#344E5C;
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
                margin-top:30px;
                color:#000!important;
                font-size:15px;
                font-weight:bold;
              }
              </style>
            </head>
            <body>
            <div class='content-fixed'>
                 <div class='wrap-image'>
                   <img src='".$img."'>
                 </div>
                 <div class='content'>
                    <p>Username:<strong>$uname</strong></p>
                    <p>Note:Please click to below link to update password.</p>
                    <p><a href='$url' class='link_verify'>Update Password</a></p>
                    <p>Owengraffix Team.</p>  
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
            echo "true";
        }
        else
        {
            echo "false";
        }

       
    }
    
    public function customer_update_password($slugid="")
    { 
        $page = app(\Litecms\Page\Interfaces\PageRepositoryInterface::class)->getPage('enupdate-password');
        return $this->response
            ->setMetaKeyword(strip_tags($page->meta_keyword))
            ->setMetaDescription(strip_tags($page->meta_description))
            ->setMetaTitle(strip_tags($page->meta_title))
            ->layout('home')
            ->view('customerupdatepassword')
            ->data(compact('slugid'))
            ->output();
    }
      
    public function customer_updatepaswords()
    {
         $password  = $_POST['password'];
         $verify_id = $_POST['verifyid'];
        
         $data  = DB::table('clients')->where('verify_id',$verify_id)->first();
        
         $values = array(
            'password'=>Hash::make($password),
         );
         $update =     DB::table('clients')
         ->where('verify_id',$verify_id)
         ->update($values);
        if($update)
        {
            echo "true";
        }
        else
        {
           echo "false"; 
        } 
    }    
    public function registers()
    {

        if(!empty(user_id()))
        {
          return redirect('client/home');
        }
        return $this->response
            ->setMetaKeyword(strip_tags("Register"))
            ->setMetaDescription(strip_tags("Register"))
            ->setMetaTitle(strip_tags("Register"))
            ->layout('home')
            ->view('register')
            ->output();
    }
    
    public function registers_action(Request $request)
    {
        
         $username  =  $request->name;
         $password  =  $request->password;
         $useremail =  $request->email;
         $verifyid  =  strtolower(substr($request->name,0,2)).time();
        
            $data = [
                'name'      => $username,
                'email'     => $useremail,
                'password'  => $password,
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
            ->where('email',$request->email)
            ->update($values);
        
            $subject                =  "Owengraffix";
            $from                   =  "info@owengraffix.com";
            $fromName               =  "Owengraffix";
            $emailSubject           =  "Owengraffix";
            $email                  =  "developer.owengraffix@gmail.com";
            $img                    =  url('public/themes/public/assets/img/oglogo-white-y.jpg');
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
                  background:#344E5C;
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
                background:#FFC639;
                padding:10px 15px;
                border-radius:50px;
                color:#fff!important;
                font-size:14px;
                font-weight:bold;
                margin-top:30px;
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
                    <p style='font-size:13px;'>Note: Please verify to login.</p>
                    <p><a href='$url' class='link_verify'>Verify email address</a></p>
                </div>
                <div class='footer'>
                    <p>From,</p>
                    <p>Owengraffix Teams</p>
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
            return redirect('login')->with("msg",""); 
        
    }
       
    public function checkemails()
    {
        $email_id = $_POST['email_id'];
        $data = DB::table('clients')->where('email',$email_id)->first();
        if($data)
        {
            echo true;
        }
        else
        {
            echo false;
        }     
    } 
    public function verifyemails($verifyid)
    { 
        $values = array(
            'status'=>"Active",
            'email_verified_at'=>date('Y-m-d'),
        ); 
        $update=DB::table('clients')
        ->where('verify_id',$verifyid)
        ->update($values);
        
        return redirect('login')->with('msg','Please login email is verified');   
    }
    
}
