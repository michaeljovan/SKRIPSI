<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    function login()
    {
        return view('login');
    }

    function loginPost(Request $request)
    {
        $request->validate([
            'user' => 'required',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $request->user, // Pastikan kolom di database adalah email
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        } else {
            return back()->withErrors(['loginError' => 'Username atau password salah']);
        }
    }

    function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
