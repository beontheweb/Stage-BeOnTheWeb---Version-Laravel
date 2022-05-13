<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;

class LoginController extends Controller
{
    /**
     * Renvoie la vue login de Auth
     */ 
    public function show()
    {
        return view('auth.login');
    }

    /**
     * Vérifie les identifiants de connexions et connecte l'utilisateur si ils sont correctes
     */
    public function login(Request $request)
    {
        $success = auth()->attempt([
            'email' => request('email'),
            'password' => request('password')
        ], request()->has('remember'));

        if($success) {
            $request->session()->regenerate();
            return redirect()->to(RouteServiceProvider::HOME);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}
