<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\User;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /** Socialite */
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    // if more than one there must be a parameter $parameter
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }
     
    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function callback($provider)
    {
               
        $data = Socialite::driver($provider)->stateless()->user();
        //dd($getInfo);

        $user = User::where('provider_id', $data->getId())->first();

        if(!$user){
            $user = User::create([
                'email' => $data->getEmail(),
                'name' => $data->getName(),
                'provider' => $provider,
                'provider_id' => $data->getId(),
                'password' => Hash::make('password')
            ]);
        }

        // login
        Auth::login($user, true);
        return redirect()->to('/home');
    }
}
