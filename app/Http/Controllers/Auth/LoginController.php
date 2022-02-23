<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Response\Auth\Response as AuthResponse;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Litepie\Theme\ThemeAndViews;
use Litepie\User\Traits\Auth\AuthenticatesUsers;
use Litepie\User\Traits\RoutesAndGuards;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Client;
use Illuminate\Support\Facades\Auth;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use RoutesAndGuards, ThemeAndViews, ValidatesRequests, AuthenticatesUsers;
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    public function __construct(Request $request = null)
    {
        $guard = request()->guard;
        guard($guard . '.web');
        $this->response = resolve(AuthResponse::class);
        $this->middleware("guest:{$guard}.web", ['except' => ['logout', 'verify', 'locked', 'sendVerification']]);
        $this->setTheme();
    }
    public function login(Request $request)
    {

    
        $data         =  $request->all();
        $request_pass =  $data['password'];
        
        

        
        if (Auth::attempt(array('email' => $data['email'], 'password' => $request_pass))){
            
            
            $userid = Auth::user()->user_id;
            $status = Auth::user()->status;
            if($userid)
            {
               $userid = Auth::user()->user_id;
                
            }
            else
            {
                $userid = user_id();
                
            }

            if($userid == 4)
            {
                if($status == "Active")
                {
                    return redirect('client'); 
                }
                else
                {
                     $request->session()->flush();
                     return redirect('client/login')->with("msg","Please verify email to activate your account!"); 
                }     
            }
                        
            if($userid == 1)
            {
                  return redirect('admin'); 
            }

            if($userid == 3)
            {
                  return redirect('admin'); 
            }
            
          
        }
        else 
        { 
            

           $roles = $_POST['roles'];
     
           if($roles == "admin") 
           {
                return redirect('admin/login')->with("msg","Invalid!");; 
           }
           else
           {
                echo "false";
           }
        }
        
    }
}
