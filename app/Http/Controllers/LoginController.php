<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    function login()
    {
        return view('login');
    }

    public function loginPost(Request $request)
    {
        $request->validate([
            'user' => 'required|email', 
            'password' => 'required',
        ]);

        // Cek apakah email terdaftar
        $user = User::where('email', $request->user)->first();

        if (!$user) {
            return back()->withErrors([
                'user' => 'Email tidak terdaftar',
            ])->withInput($request->only('user'));
        }

        $credentials = [
            'email' => $request->user,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'password' => 'Password salah',
        ])->withInput($request->only('user'));
    }

    function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
