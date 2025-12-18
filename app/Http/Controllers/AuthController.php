<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            try {
                ActivityLog::log('login', 'User', auth()->id(), 'کاربر وارد سیستم شد');
            } catch (\Exception $e) {
                // Ignore activity log errors
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'اطلاعات ورود صحیح نیست.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        try {
            ActivityLog::log('logout', 'User', auth()->id(), 'کاربر از سیستم خارج شد');
        } catch (\Exception $e) {
            // Ignore activity log errors
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
