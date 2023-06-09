<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if(Hash::check($request['password'], $user->password )){
            $request->authenticate();
            $request->session()->regenerate();
            $notification = array(
                'message' => 'Login Successfully',
                'alert-type' => 'success'
            );
            $url = '';
    
            if ($user->role === 'admin') {
                $url = 'admin/dashboard';
            } elseif ($user->role === 'vendor') {
                $url = 'vendor/dashboard';
            } elseif ($user->role === 'user') {
                $url = '/dashboard';
            }
        }else{
            $request->authenticate();
        }
        


        return redirect()->intended($url)->with($notification);
    }
 
    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
