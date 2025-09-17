<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Session\Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Services\LdapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Cache;


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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /*public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }*/

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $ldap = new LdapService();
        $ldap->setCorreo($request->email);
        $ldap->setContrasenia($request->password);

        if ($ldap->iniciarSesion()) {

            $user = new GenericUser([
                'id' => $ldap->usuario,
                'name' => $ldap->nombreCompleto,
                'email' => $request->email,
                'groups' => $ldap->grupos,
                'ldapData' => $ldap->ldapData
            ]);

            Auth::login($user);

            return redirect()->intended('home');
        }

        return back()->withErrors(['email' => 'Credenciales inválidas']);
    }

    public function logout()
    {
        //Limpia toda la session
        \Illuminate\Support\Facades\Session::flush();
        //Limpiar la cache
        Cache::flush();
        //cerrar sesión
        Auth::logout();
        //redirige hacia el login
        return redirect('/login')->withHeaders([
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0',
    ]);
    }
}
